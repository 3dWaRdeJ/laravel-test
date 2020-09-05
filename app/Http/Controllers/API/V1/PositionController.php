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

class PositionController extends Controller
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

        $positions = Position::filter($start, $length, $orderColumn, $orderDirection, $searchValue);

        $positions->each(function(Position $position) {
            $position->chiefPosition = $position->getChiefPosition();
        });
        return $this->dataTableResponse(
            $draw,
            Position::query()->count(),
            Position::filterBuilder($orderColumn, $orderDirection, $searchValue)->count(),
            $positions->toArray()
        );
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws PositionException
     * @throws ValidationException
     */
    public function store (Request $request) {
        $validate = [
            'name' => 'required|string|min:3|max:255',
            'chiefPosition' => 'integer|nullable',
        ];

        $this->validate($request, $validate);

        $name = $request->post('name');
        $chiefPositionId = $request->post('chiefPosition', null);
        $level = 1;

        // check if chief position exist
        if (is_int($chiefPositionId)) {
            $chiefPosition = Position::getById($chiefPositionId);
            if ($chiefPosition->level > 1) {
                $level = $chiefPosition->level - 1;
            } else {
                throw new PositionException('Wrong chief position, can`t be position with 1 level');
            }
        }

        $position = new Position([
            'name' => $name,
            'level' => $level,
            'chief_position_id' => $chiefPositionId
        ]);

        $position->save();

        return $this->response('New position created', $position);
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
            'chiefPosition' => 'nullable|integer',
        ];

        $this->validate($request, $validate);

        $positionId = $request->post('id');
        $position = Position::getById($positionId);

        $name = $request->post('name');
        $chiefPositionId = $request->post('chiefPosition', null);
        $level = Position::MAX_LEVEL;

        // check if chief position exist
        if (is_int($chiefPositionId)) {
            $chiefPosition = Position::getById($chiefPositionId);
            if ($chiefPosition->level <= $position->level) {
                throw new PositionException('Chief position must be higher level then current position');
            }
            $level = $chiefPosition->level - 1;
        }

        $position->name = $name;
        $position->chief_position_id = $chiefPositionId;
        $position->level = $level;
        $position->admin_update_id = auth()->user()->id;

        $position->save();

        return $this->response('Position update', $position);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Application|Response
     * @throws PositionException
     * @throws ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $positionId = $request->post('id');
        $position = Position::getById($positionId);

        $subPositions = $position->getSubPositions();

        /** @var Position $subPosition */
        foreach ($subPositions->sortBy('level') as $subPosition) {
            $subPosition->chief_position_id = null;
            $subPosition->save();
            $subPositionEmployees = $subPosition->getEmployees();
            /** @var Employee $employee */
            foreach ($subPositionEmployees as $employee) {
                $employee->setChief(null);
            }
        }

        $positionEmployees = $position->getEmployees();
        /** @var Position $randomPosition */
        $randomPosition = Position::query()->where('id', '<>', $position->id)->inRandomOrder()->first();
        /** @var Employee $positionEmployee */
        foreach ($positionEmployees as $positionEmployee) {
            $positionEmployee->setPosition($randomPosition);
        }
        $position->delete();
        return $this->response('Position deleted');
    }

}
