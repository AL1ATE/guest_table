<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guests</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Добавить</button>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Страна</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                @foreach($guests as $guest)
                    <tr>
                        <td>{{ $guest->id }}</td>
                        <td>{{ $guest->first_name }}</td>
                        <td>{{ $guest->last_name }}</td>
                        <td>{{ $guest->email }}</td>
                        <td>{{ $guest->phone }}</td>
                        <td>{{ $guest->country }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ...
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" onclick="editGuest({{ $guest->id }}, '{{ $guest->first_name }}', '{{ $guest->last_name }}', '{{ $guest->email }}', '{{ $guest->phone }}', '{{ $guest->country }}')">Обновить</button>
                                    <button class="dropdown-item" onclick="confirmDelete({{ $guest->id }})">Удалить</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Добавить гостя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" action="/guests" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="first_name">Имя</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required>
                        <span class="invalid-feedback" role="alert" id="email_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" required maxlength="15">
                        <span class="invalid-feedback" role="alert" id="phone_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="country">Страна</label>
                        <input type="text" class="form-control" id="country" name="country">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="addForm">Добавить</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Обновить данные гостя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="edit_errors" class="alert alert-danger" style="display: none;"></div>
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit_first_name">Имя</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_last_name">Фамилия</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="edit_email" name="email" required>
                        <span class="invalid-feedback" role="alert" id="edit_email_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">Телефон</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="edit_phone" name="phone" required>
                        <span class="invalid-feedback" role="alert" id="edit_phone_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit_country">Страна</label>
                        <input type="text" class="form-control" id="edit_country" name="country" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="updateGuest">Обновить</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить эту запись?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Нет</button>
                    <button type="submit" class="btn btn-danger">Да</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    function confirmDelete(id) {
        $('#deleteForm').attr('action', '/guests/' + id);
        $('#deleteModal').modal('show');
    }

    function editGuest(id, firstName, lastName, email, phone, country) {
        $('#editModal').modal('show');
        $('#editForm').attr('action', '/guests/' + id);
        $('#edit_first_name').val(firstName);
        $('#edit_last_name').val(lastName);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit_country').val(country);
    }
</script>
<script>
    $(document).ready(function () {
        $('#updateGuest').on('click', function () {
            var formData = $('#editForm').serialize();

            $.ajax({
                type: 'PUT',
                url: $('#editForm').attr('action'),
                data: formData,
                success: function (response) {
                    $('#editModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    if (xhr.status == 422) {
                        var errors = xhr.responseJSON.errors;
                        $('.invalid-feedback').text('');
                        $('.is-invalid').removeClass('is-invalid');
                        $.each(errors, function (key, value) {
                            $('#edit_' + key).addClass('is-invalid');
                            $('#edit_' + key + '_error').text(value);
                        });
                    }
                }
            });
        });

        $('#addForm').on('submit', function (e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '/guests',
                data: formData,
                success: function (response) {
                    $('#addModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $('.invalid-feedback').text('');
                        $('.is-invalid').removeClass('is-invalid');
                        $.each(errors, function (key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').html(value);
                        });
                    }
                }
            });
        });
    });
</script>
</body>
</html>
