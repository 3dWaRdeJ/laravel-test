@extends('layouts.app')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Positions</h3>
                    <div class="project-actions text-right">
                        <button id="openCreateModal" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus"></i>
                            Add position
                        </button>
                    </div>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <table id="positionsTable" class="table table-bordered table-striped projects">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Last update</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Last update</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- Remove Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Remove employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="removeModal-text">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="removeButton" class="btn btn-primary">Remove</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            id="inputName"
                            placeholder="Enter name"
                            aria-describedby="fullName-error"
                            maxlength="255"
                            minlength="3"
                            required
                        >
                        <div class="row">
                            <div class="col-md-8">
                                <span id="inputName-error" class="error invalid-feedback">Please enter a name</span>
                            </div>
                            <div class="col-md-4 text-right text-gray">
                                <span id="inputName-length" class="">0/255</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="selectChiefPosition">Chief Position</label>
                        <select class="form-control custom-select" name="position" id="selectChiefPosition">
                            <option value="">No chief position</option>
                        </select>
                        <span id="selectChiefPosition-error" class="error invalid-feedback">Please select chief position</span>
                    </div>
                    <div class="row" id="updateModal-additionalData">
                        <div class="col-sm-6">
                            <strong>Created at:&nbsp;</strong>
                            <div id="updateModal-createAt"></div>
                        </div>
                        <div class="col-sm-6" >
                            <strong>Admin create ID:&nbsp;</strong>
                            <div id="updateModal-createAdmin"></div>
                        </div>
                        <div class="col-sm-6">
                            <strong>Updated at:&nbsp;</strong>
                            <div id="updateModal-updateAt"></div>
                            <br>
                        </div>
                        <div class="col-sm-6">
                            <strong>Admin update ID:&nbsp;</strong>
                            <div id="updateModal-updateAdmin"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="createButton" class="btn btn-primary">Add</button>
                    <button type="button" id="updateButton" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>

    <script>
        function createHTMLOption(value, text) {
            return '<option value="' + value + '">' + text + '</option>';
        }

        function createHTMLUpdateButton(id) {
            return '<button class="btn btn-info btn-sm" onclick="openUpdateModal(' + id + ')" data-toggle="modal" data-target="#createModal">' +
                '<i class="fas fa-pencil-alt"></i>' +
                '</button>'
        }

        function createHTMLRemoveButton(id) {
            return '<button type="button" onclick="openRemoveModal(' + id + ')" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#removeModal">' +
                '<i class="fas fa-trash"></i>' +
                '</button>'
        }

        //create action buttons
        function createHTMLForAction(id) {
            let updateButton = createHTMLUpdateButton(id);
            let removeButton = createHTMLRemoveButton(id);
            return updateButton + removeButton;
        }

        function modifyPositions(positions) {
            positions.forEach(function(position, i, employees) {
                position = modifyPosition(position);
            })
            return positions;
        }

        function modifyPosition(position) {
            position.action = createHTMLForAction(position.id);
            return position;
        }

        let table = $('#positionsTable');
        let apiToken = '{{$apiToken}}';
        let apiUrl = {
            positions: '/api/v1/position'
        }

        let dataTable = table.DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: apiUrl.positions + '?' + $.param({
                    api_token: apiToken
                }),
                dataSrc: function(json) {
                    let positions = json.data;
                    positions = modifyPositions(positions)
                    return positions;
                }
            },
            columns: [
                {data: 'name'},
                {data: 'updated_at'},
                {
                    data: 'action',
                    searchable: false,
                    orderable: false
                },
            ],
            order: [[0, 'asc']]
        });

        let removePosId = null;
        function openRemoveModal(id) {
            let positions = dataTable.data();
            let removePosition = null;
            for ([key, position] of Object.entries(positions)) {
                if (position.id = id) {
                    removePosition = position;
                    break;
                }
            }
            $('#removeModal-text').text('Are you sure want to remove position ' + removePosition.name);
            removePosId = removePosition.id;
        }

        function openUpdateModal(id) {
            $('#updateButton').show();
            $('#createButton').hide();
            $('#updateModal-additionalData').show();

            let updatePosition = null;
            for ([key, tablePos] of Object.entries(dataTable.data())) {
                if (tablePos.id == id) {
                    updatePosition = tablePos;
                    break;
                }
            }

            $('#updateModal-createAdmin').text(updatePosition.admin_create_id);
            $('#updateModal-updateAdmin').text(updatePosition.admin_update_id);
            $('#updateModal-createAt').text(updatePosition.created_at);
            $('#updateModal-updateAt').text(updatePosition.updated_at);

            $('#inputName').val(updatePosition.name)
            $('#inputName-length').text(updatePosition.name.length + '/255');

            $('#selectChiefPosition').empty();

            let chiefPosition = updatePosition.chiefPosition;
            if (chiefPosition !== null) {
                if (positions[chiefPosition.id] === undefined) {
                    $('#selectChiefPosition').append(createHTMLOption(chiefPosition.id, chiefPosition.name));
                }
            }

            $('#selectChiefPosition').append(createHTMLOption('', 'No chief'));
            positions.forEach(function (position, i , arr) {
                if (position.level > updatePosition.level) {
                    $('#selectChiefPosition').append(createHTMLOption(position.id, position.name));
                }
            });
            if (chiefPosition === null) {
                $('#selectChiefPosition').val(null)
            } else {
                $('#selectChiefPosition').val(chiefPosition.id)

            }
            updatePositionId = updatePosition.id;
        }

        let updatePositionId = null;
        $('#updateButton').on('click', function() {
           let bodyParams = {
               api_token: apiToken,
               id: updatePositionId,
               name: $('#inputName').val(),
               chiefPosition: $('#selectChiefPosition').val()
           };

           $.ajax({
               type: 'PUT',
               url: apiUrl.positions,
               contentType: 'application/json',
               dataType: 'text',
               data: JSON.stringify(bodyParams),
               success: function(response) {
                   dataTable.ajax.reload();
                   $('#createModal').modal('hide');
               },
               error: function (response) {
                   if (response.status == 422) {
                       let fails = JSON.parse(response.responseText).fails;
                       for (const key in fails) {
                           let inputId = '';
                           switch (key) {
                               case 'chief_position':
                                   inputId = 'selectChiefPosition';
                                   break;
                               default:
                                   inputId = 'input' + key.slice(0, 1).toUpperCase() + key.slice(1);
                           }
                           updateInput(inputId, false, fails[key][0])
                       }
                   }
               }
           });
        });

        function openCreateModal() {
            $('#updateButton').hide()
            $('#createButton').show();
            $('#updateModal-additionalData').hide();
            let inputs = $('#inputName, #inputLevel, #selectChiefPosition');
            inputs.val(null);
            inputs.removeClass('is-valid');
            inputs.removeClass('is-invalid');
            $('#inputName-length').text('0/255');
            $('#selectChiefPosition').empty();
            $('#selectChiefPosition').append(createHTMLOption('', 'No chief'));

            positions.forEach(function(position, i , arr) {
                if (position.level > 1) {
                    $('#selectChiefPosition').append(createHTMLOption(position.id, position.name));
                }
            });
        }

        $('#openCreateModal').on('click', function() {
            openCreateModal();
        });


        function validateName(inputId) {
            let message = '';
            let isValid = false;
            let name = $('#' + inputId).val();

            if (name.length < 3) {
                message = 'Position name must be at least 3 chars'
            } else if (name.length > 255) {
                message = 'Position name must be less then 255 chars'
            } else {
                isValid = true;
            }

            updateInput(inputId, isValid, message);
        }

        $('#inputName').on('change, input', function() {
            $('#inputName-length').text(this.value.length + '/255');
            validateName(this.id);
        })

        $('#createButton').on('click', function() {
            let bodyParams = {
                api_token: apiToken,
                name: $('#inputName').val(),
                chief_position: $('#selectChiefPosition').val()
            }

            $.ajax({
                type: 'POST',
                url: apiUrl.positions,
                contentType: 'application/json',
                dataType: 'text',
                data: JSON.stringify(bodyParams),
                success: function() {
                    dataTable.ajax.reload();
                    $('#createButton').modal('hide');
                },
                error: function (response) {
                   if (response.status == 422) {
                        let fails = JSON.parse(response.responseText).fails;
                        for (const key in fails) {
                            let inputId = '';
                            switch (key) {
                                case 'chief_position':
                                    inputId = 'selectChiefPosition';
                                    break;
                                default:
                                    inputId = 'input' + key.slice(0, 1).toUpperCase() + key.slice(1);
                            }
                            updateInput(inputId, false, fails[key][0])
                        }
                    }
                }
            })
        });

        $('#removeButton').on('click', function() {
            let bodyParams = {
                id: removePosId,
                api_token: apiToken
            }
            $.ajax({
                type: 'DELETE',
                url: apiUrl.positions,
                contentType: 'application/json',
                dataType: 'text',
                data: JSON.stringify(bodyParams),
                success: function (response) {
                    removePosId = null;
                    dataTable.ajax.reload();
                    $('#removeModal').modal('hide');
                },
                error: function (response) {
                    removePosId = null;
                    alert('Server error when delete position');
                    $('#removeModal').modal('hide');
                }
            });
        })

        let positions = [];
        function downloadPositions(offset = 0, count = 100) {
            let queryParams = $.param({
                api_token: apiToken,
                start: offset,
                length: count
            });
            $.ajax({
                type: 'GET',
                url: apiUrl.positions + '?' + queryParams,
                success: function (response) {
                    for ([key, position] of Object.entries(response.data)) {
                        positions[position.id] = position;
                    }
                    if (response.data.length == count) {
                        downloadPositions(offset + count, count);
                    }
                }
            })
        }
        downloadPositions();

        function updateInput(id, isValid, text) {
            let input = $('#' + id);
            let inputMessage = $('#' + id + '-error');
            inputMessage.text(text);
            if (isValid === true) {
                input.removeClass('is-invalid');
                input.addClass('is-valid');
                inputMessage.hide();
            } else {
                input.removeClass('is-valid');
                input.addClass('is-invalid');
                inputMessage.show();
            }
            return isValid;
        }
    </script>
@endsection
