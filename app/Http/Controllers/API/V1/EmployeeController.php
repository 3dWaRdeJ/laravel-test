<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\EmployeeException;
use App\Exceptions\PositionException;
use App\Position;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws ValidationException
     */
    public function search(Request $request)
    {
        $validate = [
            'draw' => 'integer',
            'start' => 'integer',
            'length' => 'integer|max:1000|min:1',
            'order' => 'array',
            'order.*.column' => 'integer',
            'order.*.dir' => 'in:asc,desc',
            'columns' => 'array',
            'columns.*.data' => 'string|nullable',
            'columns.*.name' => 'string|nullable',
            'columns.*.searchable' => 'string|in:true,false',
            'columns.*.orderable' => 'string|in:true,false',
            'search' => 'array',
            'search.value' => 'string|nullable'
        ];
        $this->validate($request, $validate);

        $draw = $request->post('draw', 0);
        $start = $request->query('start', 0);
        $order = $request->query('order', []);
        $length = $request->query('length', 10);
        $columns = $request->query('columns', []);
        $search = $request->query('search', []);

        $orderColumn = 'id';
        $orderDirection = 'asc';
        $searchValue = '';

        if (isset($order[0])
            && isset($order[0]['column'])
        ) {
            $orderColumnIndex = $order[0]['column'];
            $orderDirection = $order[0]['dir'];
            if (isset($columns[$orderColumnIndex])) {
                $column = $columns[$orderColumnIndex];
                if ($column['orderable'] === 'true') {
                    $orderColumn = $column['data'];
                }
            }
        }

        if (isset($search['value'])) {
            $searchValue = $search['value'];
        }

        $employees = Employee::filter($start, $length, $orderColumn, $orderDirection, $searchValue);

        $employees->each(function (Employee $employee) {
            $employee->position = $employee->getPosition();
            $employee->chief = $employee->getChief();
        });

        return $this->dataTableResponse(
            $draw,
            Employee::query()->count(),
            Employee::filterBuilder($orderColumn, $orderDirection, $searchValue)->count(),
            $employees->toArray()
        );
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws ValidationException
     * @throws EmployeeException
     * @throws PositionException
     * @throws FileNotFoundException
     */
    public function store (Request $request) {
        $validate = [
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|regex:/^\+380\d{9}$/',
            'email' => 'required|string|email|max:255',
            'position' => 'required|integer',
            'salary' => 'required|numeric|between:0,500000',
            'startDate' => 'required|date|regex:/^\d{4}\-\d{2}\-\d{2}$/',
            'chief' => 'integer|nullable',
            'photo' => 'file|mimes:png,jpeg|dimensions:min_width=300,min_height=300|max:' . 5 * 1024
        ];

        $this->validate($request, $validate);

        $name = $request->post('name');
        $phone = $request->post('phone');
        $email = $request->post('email');
        $positionId = $request->post('position');
        $salary = $request->post('salary');
        $startDate = $request->post('startDate');
        $chiefId = $request->post('chief', null);
        $photo = $request->file('photo', null);

        //check if position exist
        $employeePosition = Position::getById($positionId);

        if (is_int($chiefId)) {
            $this->validateChief($employeePosition, $chiefId);
        }

        $employee = new Employee([
            'full_name' => $name,
            'phone' => $phone,
            'email' => $email,
            'position_id' => $positionId,
            'salary' => $salary,
            'start_date' => $startDate,
            'chief_id' => $chiefId,
        ]);

        if ($photo instanceof UploadedFile) {
            $employee->savePhoto($photo);
        }

        $employee->save();

        return $this->response('New employee created', $employee);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws EmployeeException
     * @throws FileNotFoundException
     * @throws PositionException
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $validate = [
            'id' => 'required|integer',
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|regex:/^\+380\d{9}$/',
            'email' => 'required|string|email|max:255',
            'position' => 'required|integer',
            'salary' => 'required|numeric|between:0,500000',
            'startDate' => 'required|date|regex:/^\d{4}\-\d{2}\-\d{2}$/',
            'chief' => 'integer|nullable',
            'photo' => 'file|mimes:png,jpeg|dimensions:min_width=300,min_height=300|max:' . 5 * 1024
        ];

        $this->validate($request, $validate);

        $employeeId = $request->post('id');
        $name = $request->post('name');
        $phone = $request->post('phone');
        $email = $request->post('email');
        $positionId = $request->post('position');
        $salary = $request->post('salary');
        $startDate = $request->post('startDate');
        $chiefId = $request->post('chief', null);
        $photo = $request->file('photo', null);

        //check if position exist
        $employeePosition = Position::getById($positionId);

        if (is_int($chiefId)) {
            $this->validateChief($employeePosition, $chiefId);
        }

        $admin = auth()->user();
        $employee = Employee::getById($employeeId);
        $employee->full_name = $name;
        $employee->phone = $phone;
        $employee->email = $email;
        $employee->position_id = $positionId;
        $employee->salary = $salary;
        $employee->start_date = $startDate;
        $employee->chief_id = $chiefId;
        $employee->admin_update_id = $admin->id;

        if ($photo instanceof UploadedFile) {
            $employee->savePhoto($photo);
        }

        $employee->save();

        return $this->response('Employee updated', $employee);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws EmployeeException
     * @throws ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $employeeId = $request->post('id');
        $employee = Employee::getById($employeeId);

        $subEmployees = $employee->getSubEmployees();

        /** @var Employee $subEmployee */
        foreach ($subEmployees as $subEmployee) {
            $subEmployee->setRandomChief($employee);
            $subEmployee->save();
        }

        $employee->deletePhoto();
        $employee->delete();
        return $this->response('Employee deleted');
    }

    /**
     * @param Position $employeePosition
     * @param int $chiefId
     * @throws EmployeeException
     */
    protected function validateChief(Position $employeePosition, int $chiefId) {
        $employeeChief = Employee::getById($chiefId, true);

        //check is employee chief position is correct in position hierarchy
        $chiefPositions = $employeePosition->getChiefPositions();
        if ($chiefPositions->contains($employeeChief->position) == false) {
            throw new EmployeeException('Wrong chief, employee and chief in different hierarchy branch');
        }
    }
}
