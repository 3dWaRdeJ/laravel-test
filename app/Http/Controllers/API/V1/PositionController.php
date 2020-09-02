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
            'offset' => 'integer',
            'count' => 'integer|max:1000|min:1',
            'withChief' => 'boolean'
        ];

        $this->validate($request, $validate);

        $offset = $request->query('offset', 0);
        $count = $request->query('count', 10);
        $withChief = $request->query('withChief', false);

        $position = Position::search($offset, $count, $withChief);

        return $this->response('', $position);
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
            'chiefPosition' => 'integer',
            'level' => 'required_without:chiefPosition|integer|min:1|max:' . Position::MAX_LEVEL
        ];

        $this->validate($request, $validate);

        $name = $request->post('name');
        $chiefPositionId = $request->post('chiefPosition', null);
        $level = $request->post('level', 1);

        // check if chief position exist
        if (is_int($chiefPositionId)) {
            $chiefPosition = Position::getById($chiefPositionId);
            if ($level > $chiefPosition->level) {
                throw new PositionException('Wrong chief position - must be at least same level');
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
            'level' => 'required|integer|min:1|max:' . Position::MAX_LEVEL
        ];

        $this->validate($request, $validate);

        $positionId = $request->post('id');
        $position = Position::getById($positionId);

        $name = $request->post('name');
        $chiefPositionId = $request->post('chiefPosition', null);
        $level = $request->post('level');

        // check if chief position exist
        if (is_int($chiefPositionId)) {
            $chiefPosition = Position::getById($chiefPositionId);
            if ($level > $chiefPosition->level) {
                throw new PositionException('Chief position must be at least same level');
            }
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
            'withChildPosition' => 'boolean'
        ]);

        $positionId = $request->post('id');
        $withChildPos = $request->post('withChildPosition', false);
        $position = Position::getById($positionId);

        if ($withChildPos) {
            $subPositions = $position->getSubPositions(1);
        } else {
            $subPositions = $position->getSubPositions();
        }

        /** @var Position $subPosition */
        foreach ($subPositions->sortBy('level') as $subPosition) {
            $subPosition->chief_position_id = null;
            $subPosition->save();
            if ($withChildPos) {
                $subPosition->delete();
            }
        }

        $position->delete();
        return $this->response('Position deleted');
    }

}
