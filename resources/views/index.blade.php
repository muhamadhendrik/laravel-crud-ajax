<!DOCTYPE html>
<html>

<head>
    <title>Score CRUD</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
</head>

<body>

    <div class="container my-4" >
        <a class="btn btn-success my-2" href="javascript:void(0)" id="createNewScore"> Create New Score</a>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Score</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="scoreForm" name="scoreForm" class="form-horizontal">
                        <input type="hidden" name="score_id" id="score_id">
                        <div class="form-group">
                            <label for="student" class="col-sm-2 control-label">Student</label>
                            <select name="student_id" class="form-control" id="student_id">
                                <option disabled selected>pilih</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="col-sm-2 control-label">subject</label>
                            <select name="subject_id" class="form-control" id="subject_id">
                                <option disabled selected>pilih</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="col-sm-2 control-label">Score</label>
                            <input type="text" id="score" class="form-control" name="score">
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

<script type="text/javascript">
    $(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('score.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'student.name',
                    name: 'student_id'
                },
                {
                    data: 'subject.name',
                    name: 'subject_id'
                },
                {
                    data: 'score',
                    name: 'score'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#createNewScore').click(function () {
            $('#saveBtn').val("create-score");
            $('#score_id').val('');
            $('#scoreForm').trigger("reset");
            $('#modelHeading').html("Create New score");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editScore', function () {
            console.log($('select[id=student_id] option').filter(':selected').val())
            var score_id = $(this).data('id');
            $.get("{{ route('score.index') }}" + '/' + score_id + '/edit', function (data) {
                $('#modelHeading').html("Edit score");
                $('#saveBtn').val("edit-user");
                $('#ajaxModel').modal('show');
                $('#score_id').val(data.id);
                $('select[id=student_id]').children(`option[value=${data.student_id}]`).attr("selected", '');
                $('select[id=subject_id]').children(`option[value=${data.subject_id}]`).attr("selected", '');
                $('#score').val(data.score);
            })
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();

            $.ajax({
                data: $('#scoreForm').serialize(),
                url: "{{ route('score.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {

                    $('#scoreForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();

                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save Changes');
                }
            });
        });

        $('body').on('click', '.deleteScore', function () {

            var score_id = $(this).data("id");
            confirm("Are You sure want to delete !");

            $.ajax({
                type: "DELETE",
                url: "{{ route('score.store') }}" + '/' + score_id,
                success: function (data) {
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });

    });
</script>

</html>
