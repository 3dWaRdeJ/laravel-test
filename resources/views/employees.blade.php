@extends('layouts.app')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employees</h3>
                    <div class="project-actions text-right">
                        <button id="openCreateModal" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal" onclick="openCreateModal()">
                            <i class="fas fa-user-plus"></i>
                            Add employee
                        </button>
                    </div>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped projects">
                        <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Date of employment</th>
                            <th>Phone number</th>
                            <th>Email</th>
                            <th>Salary</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Date of employment</th>
                            <th>Phone number</th>
                            <th>Email</th>
                            <th>Salary</th>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Remove employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="removeModalBody">
                    Are you sure want to remove employee
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="removeButton" class="btn btn-primary">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Update Modal -->
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
                        <label for="inputPhoto-img">Employee photo</label>
                        <div class="text-center">
                            <img id="inputPhoto-img" class="profile-user-img img-fluid img-circle" src="{{ App\Employee::DEFAULT_PHOTO_PATH }}">
                        </div>
                        <label class="text-gray">File format png/jpeg up to 5MB, the minimum size 300x300px</label>
                        <div class="custom-file">
                            <input type="file" accept="image/jpeg,image/png" class="custom-file-input" id="inputPhoto" name="photo">
                            <label class="custom-file-label" for="customFile" id="inputPhoto-name">Choose photo</label>
                        </div>
                        <span id="inputPhoto-error" class="error invalid-feedback"></span>
                    </div>
                    <input type="hidden" id="inputId" name="id">
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
                            minlength="2"
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
                        <label for="inputPhone">Phone</label>
                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            id="inputPhone"
                            placeholder="Enter phone"
                            aria-describedby="inputPhone-error"
                            required
                        >
                        <span id="inputPhone-error" class="error invalid-feedback">Please enter a phone</span>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            id="inputEmail"
                            placeholder="Enter email"
                            aria-describedby="Email-error"
                            maxlength="255"
                        >
                        <span id="inputEmail-error" class="error invalid-feedback">Please enter a email</span>
                    </div>
                    <div class="form-group">
                        <label for="selectPosition">Position</label>
                        <select class="form-control custom-select" name="position" id="selectPosition">
                        </select>
                        <span id="selectPosition-error" class="error invalid-feedback">Please select position</span>
                    </div>
                    <div class="form-group">
                        <label for="inputSalary">Salary, $</label>
                        <input
                            type="number"
                            name="salary"
                            class="form-control"
                            id="inputSalary"
                            placeholder="Enter salary"
                            aria-describedby="inputSalary-error"
                            max="500000"
                            min="0"
                        >
                        <span id="inputSalary-error" class="error invalid-feedback">Please enter salary</span>
                    </div>
                    <div class="form-group" id="chiefFormGroup">
                        <label for="selectChief">Chief</label>
                        <select class="form-control custom-select" name="chief_id" id="selectChief">
                            <option value="">No chief</option>
                        </select>
                        <span id="selectChief-error" class="error invalid-feedback">Please chose chief</span>
                    </div>
                    <!-- Date -->
                    <div class="form-group">
                        <label>Date:</label>
                        <div class="input-group date" id="startDate" data-target-input="nearest">
                            <div class="input-group-append" data-target="#startDate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            <input type="text" id="inputStartDate" class="form-control datetimepicker-input" data-target="#startDate" name="start_date"/>
                            <span id="inputStartDate-error" class="error invalid-feedback">Please enter date</span>
                        </div>
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

    <!-- InputMask -->
    <script src="{{asset('plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
    <!-- date-range-picker -->
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <!-- Bootstrap Switch -->
    <script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
    <!-- page script -->
    <script>
        //Date range picker
        $('#startDate').datetimepicker({
            format: 'DD.MM.Y'
        });
    </script>
    <script>
        function createHTMLImg(photoPath) {
            return '<img alt="Avatar" class="table-avatar" src="' + photoPath + '">';
        }
        //create HTML for table cell
        function createHTMLForName (name, photoPath) {
            return '<ul class="list-inline">'
                + '<li class="list-inline-item">'
                + '<img alt="Avatar" class="table-avatar" src="' + photoPath + '">'
                + '</li>'
                + '<li class="list-inline-item">'
                + name
                + '</li>'
                + '</ul>'
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

        function updateEmployees(employees) {
            employees.forEach(function(employee, i, employees) {
                employee = updateEmployee(employee);
            })
            return employees;
        }

        function updateEmployee(employee) {
            if (employee.photo_path === null) {
                employee.photo_path = '{{\App\Employee::DEFAULT_PHOTO_PATH}}';
            }
            employee.photo = createHTMLImg(employee.photo_path);
            employee.action = createHTMLForAction(employee.id);
            return employee;
        }

        let table = $('#example1');
        let apiToken = '{{$apiToken}}';
        let apiUrl = {
            employee: '/api/v1/employee',
            position: '/api/v1/position'
        }

        let dataTable = table.DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: apiUrl.employee + '?' + $.param({
                    api_token: apiToken
                }),
                dataSrc: function(json) {
                    let employees = json.data;
                    employees = updateEmployees(employees)
                    return employees;
                }
            },
            columns: [
                {
                    data: 'photo',
                    searchable: false,
                    orderable: false
                },
                {data: 'full_name'},
                {data: 'positionName'},
                {data: 'start_date'},
                {data: 'phone'},
                {data: 'email'},
                {data: 'salary'},
                {
                    data: 'action',
                    searchable: false,
                    orderable: false
                },
            ],
            order: [[1, 'asc']]
        });
        let removeEmployeeId = null;
        function openRemoveModal(id) {
            removeEmployeeId = id;
            let employees = dataTable.data();
            let employee = null;
            for ([key, empObj] of Object.entries(employees)) {
                if (empObj.id == id) {
                    employee = empObj;
                    break;
                }
            }
            $('#removeModalBody').text('Are you sure want to remove employee ' + employee.full_name);
        }

        $('#removeButton').on('click', function () {
           let bodyParams = {
               id: removeEmployeeId,
               api_token: apiToken
           };
           $.ajax({
               type: 'DELETE',
               url: apiUrl.employee,
               contentType: 'application/json',
               dataType: 'text',
               data: JSON.stringify(bodyParams),
               success: function (response) {
                   removeEmployeeId = null;
                   dataTable.ajax.reload();
                   $('#removeModal').modal('hide');
               },
               error: function (data) {
                   alert('error when delete employee');
               }
           })
        });

        function createHTMLOption(value, text) {
            return '<option value="' + value + '">' + text + '</option>';
        }

        let positions = {};
        function updatePositionSelect(selectId)
        {
            let select = $('#' + selectId);
            let prevValue = select.val();
            select.empty();
            select.append(createHTMLOption('', 'No chief position'));
            for ([key, position] of Object.entries(positions)) {
                select.append(createHTMLOption(position.id, position.name));
            }
            select.val(prevValue);
        }

        function downloadPositions(offset = 0, count = 200) {
            $.ajax({
                type: 'GET',
                url: apiUrl.position + '?' + $.param({
                    api_token: apiToken,
                    start: offset,
                    length: count
                }),
                success: function (response) {
                    let newPositions = response.data;
                    newPositions.forEach(function(position, i , arr) {
                        positions[position.id] = position;
                    });
                    updatePositionSelect('selectPosition');
                    if (newPositions.length == count) {
                        downloadPositions(offset + count, count);
                    }
                }
            })
        }
        downloadPositions();

        let employees = {};
        function downloadEmployees(offset = 0, count = 1000) {
            $.ajax({
                type: 'GET',
                url: apiUrl.employee + '?' + $.param({
                    api_token: apiToken,
                    start: offset,
                    length: count,
                }),
                success: function(response) {
                    let newEmployees = response.data;
                    newEmployees.forEach(function(newEmployee, i, arr) {
                        newEmployee
                        employees[newEmployee.id] = newEmployee;
                    });
                    updateChiefSelect('selectChief');
                    if (newEmployees.length == count) {
                        downloadEmployees(offset + count, count);
                    }
                }
            });
        }
        downloadEmployees();

        function updateChiefSelect(selectId)
        {
            let select = $('#' + selectId);
            let prevValue = select.val();
            select.empty();
            select.append(createHTMLOption('', 'No chief'));
            for ([key, employee] of Object.entries(employees)) {
                select.append(createHTMLOption(employee.id, employee.full_name));
            }
            select.val(prevValue);
        }

        function openCreateModal()
        {
            $('#createButton').show();
            $('#updateButton').hide();
            $('#updateModal-additionalData').hide();

            //clear input data
            let inputs = $('#inputPhoto, #inputName, #inputPhone, #inputEmail, #inputSalary, #inputStartDate, #selectChief, #selectPosition');
            $('#inputPhoto-name').text('Choose photo');
            $('#inputName-length').text('0/255');
            updatePositionSelect('selectPosition');
            inputs.val(null);
            inputs.removeClass('is-valid');
            inputs.removeClass('is-invalid');
        }

        function openUpdateModal(id) {
            $('#inputId').val(id);
            $('#createButton').hide();
            $('#updateButton').show();
            $('#updateModal-additionalData').show();

            let inputs = $('#inputPhoto, #inputName, #inputPhone, #inputEmail, #inputSalary, #inputStartDate, #selectChief, #selectPosition');

            $('#inputPhoto-name').text('Choose photo');
            inputs.val(null);
            inputs.removeClass('is-valid');
            inputs.removeClass('is-invalid');

            let tableEmployees = dataTable.data();
            for ([key, tableEmployee] of Object.entries(tableEmployees)) {
                if (tableEmployee.id == id) {
                    let employee = tableEmployees[key];
                    console.log(employee);
                    $('#inputName').val(employee.full_name);
                    $('#inputName-length').text(employee.full_name.length + '/255');
                    $('#inputPhone').val(employee.phone);
                    $('#inputEmail').val(employee.email);
                    if (positions[employee.position_id] === undefined) {
                        positions[employee.position_id] = employee.position;
                    }
                    updatePositionSelect('selectPosition')
                    $('#selectPosition').val(employee.position_id);
                    $('#inputSalary').val(employee.salary);

                    if (employee.chief_id !== null
                        && employees[employee.chief_id] === undefined
                        && employee.chief !== null
                    ) {
                        employees[employee.chief_id] = employee.chief;
                    }
                    updateChiefSelect('selectChief');
                    $('#selectChief').val(employee.chief_id);
                    $('#inputStartDate').val(employee.start_date);

                    $('#updateModal-updateAt').text(employee.updated_at);
                    $('#updateModal-updateAdmin').text(employee.admin_update_id);
                    $('#updateModal-createAt').text(employee.created_at);
                    $('#updateModal-createAdmin').text(employee.admin_create_id);
                    break;
                }
            }
        }

        $('#createButton').on('click', function() {
            if (validateForm()) {
                let data = new FormData();
                data.append('name', $('#inputName').val());
                data.append('phone', $('#inputPhone').val());
                data.append('email', $('#inputEmail').val());
                data.append('position', $('#selectPosition').val());
                data.append('salary', $('#inputSalary').val());
                data.append('startDate', $('#inputStartDate').val());
                let chiefId = $('#selectChief').val();
                if (chiefId.length > 0) {
                    data.append('chief', chiefId);
                }
                let photo = $('#inputPhoto')[0].files[0];
                if (photo !== undefined) {
                    data.append('photo', photo);
                }

                $.ajax({
                    url: apiUrl.employee + '?' + $.param({api_token: apiToken}),
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        dataTable.ajax.reload();
                        $('#createModal').modal('hide');
                    },
                    error: function (response) {
                        let fails = response.responseJSON.fails;
                        for (const key in fails) {
                            let inputId = '';
                            switch (key) {
                                case 'position_id':
                                    inputId = 'selectPosition';
                                    break;
                                case 'chief_id':
                                    inputId = 'selectChief';
                                    break;
                                default:
                                    inputId = 'input' + key.slice(0, 1).toUpperCase() + key.slice(1);
                            }
                            updateInput(inputId, false, fails[key][0])
                        }
                    }
                })
            }
        });

        $('#updateButton').on('click', function() {
            if (validateForm()) {
                let data = new FormData();
                data.append('id', $('#inputId').val());
                data.append('name', $('#inputName').val());
                data.append('phone', $('#inputPhone').val());
                data.append('email', $('#inputEmail').val());
                data.append('position', $('#selectPosition').val());
                data.append('salary', $('#inputSalary').val());
                data.append('startDate', $('#inputStartDate').val());
                let chiefId = $('#selectChief').val();
                if (chiefId.length > 0) {
                    data.append('chief', chiefId);
                }
                let photo = $('#inputPhoto')[0].files[0];
                if (photo !== undefined) {
                    data.append('photo', photo);
                }

                $.ajax({
                    url: apiUrl.employee + '?' + $.param({
                        api_token: apiToken,
                        _method: 'PUT'
                    }),
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        dataTable.ajax.reload();
                        $('#createModal').modal('hide');
                    },
                    error: function (response) {
                        let fails = response.responseJSON.fails;
                        for (const key in fails) {
                            let inputId = '';
                            switch (key) {
                                case 'position_id':
                                    inputId = 'selectPosition';
                                    break;
                                case 'chief_id':
                                    inputId = 'selectChief';
                                    break;
                                default:
                                    inputId = 'input' + key.slice(0, 1).toUpperCase() + key.slice(1);
                            }
                            updateInput(inputId, false, fails[key][0])
                        }
                    }
                })
            }
        });

        function validateForm() {
            let name = validateName('inputName');
            let phone = validatePhone('inputPhone');
            let email = validateEmail('inputEmail');
            let salary = validateSalary('inputSalary');
            let startDate = validateStartDate('inputStartDate');
            let isValidData = name && phone && email && salary && startDate;
            if ($('#inputPhoto')[0].files[0] !== undefined) {
                isValidData = isValidData && validatePhoto('inputPhoto');
            }
            return isValidData;
        }

        function validatePhoto(id) {
            let message = '';
            let isValid = false;
            let photoFile = $('#' + id)[0].files[0];

            if (photoFile.type !== 'image/png' && photoFile.type !== 'image/jpeg') {
                message = 'wrong file type must be png or jpg';
            } else if (photoFile.size > 1024 * 1024 * 1) {
                message = 'photo file size must be less then 5 MB'
            } else {
                isValid = true;
            }

            updateInput(this.id, isValid, message);
            if (isValid) {
                $('#' + id + '-name').text(photoFile.name);
            } else {
                $('#' + id).val(null);
                $('#' + id + '-name').text('Choose file');
            }
            return isValid;
        }

        $('#inputPhoto').on('change, input', function() {
            let preview = $('#inputPhoto-img');
            let file = this.files[0];
            let reader = new FileReader();

            reader.onloadend = function () {
                preview.attr('src', reader.result);
            }

            if (validatePhoto(this.id)) {
                reader.readAsDataURL(file);
            }
        })

        function validateName (id) {
            let message = '';
            let isValid = false;
            let name = $('#' + id).val();
            if (name.length < 3) {
                message = 'Name must have at least 3 characters';
            } else if (name.length > 255) {
                message = 'Name must have less then 255 characters';
            } else {
                isValid = true;
            }
            updateInput(id, isValid, message);
            return isValid;
        }

        $('#inputName').on('change, input', function() {
            $('#inputName-length').text(this.value.length + '/255');
            validateName(this.id);
        });

        function validatePhone(id) {
            let regEx = /^\+380\d{9}$/;
            let isValid = false;
            let message = '';
            let phone = $('#' + id).val();
            if (regEx.test(phone) == false) {
                message = 'phone must coincide pattern +380XXXXXXXXX';
            } else {
                isValid = true;
            }
            updateInput(id, isValid, message);
            return isValid;
        }

        $('#inputPhone').on('change, input', function() {
            validatePhone(this.id);
        });

        function validateEmail(id) {
            let regEx = /^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/;
            let isValid = false;
            let message = '';
            let email = $('#' + id).val();
            if (regEx.test(email) == false) {
                message = 'Incorrect Email';
            } else {
                isValid = true;
            }
            updateInput(id, isValid, message);
            return isValid;
        };

        $('#inputEmail').on('change, input', function() {
            validateEmail(this.id);
        });

        function validateSalary(id) {
            let isValid = false;
            let message = '';
            let salary = $('#' + id).val();
            if (salary < 0) {
                message = 'Incorrect salary must be positive value';
            } else if (salary > 500000) {
                message = 'Incorrect salary must be less then 500 000';
            } else if (salary.length === 0) {
                message = 'Input salary value';
            } else{
                isValid = true;
            }
            updateInput(id, isValid, message);
            return isValid;
        }
        $('#inputSalary').on('change, input', function () {
            validateSalary(this.id);
        });

        function validateStartDate(id) {
            let isValid = false;
            let message = '';
            let date = $('#' + id).val();
            let regEx = /^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.\d{4}$/;
            if (regEx.test(date) == false) {
                message = 'Wrong date format, must be like \'31.01.2020\'';
            } else if (Date.parse(date) === NaN) {
                message = 'Wrong date';
            } else {
                isValid = true;
            }
            updateInput(id, isValid, message);
            return isValid;
        }

        $('#inputStartDate').on('change, input', function () {
            validateStartDate(this.id);
        });

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
