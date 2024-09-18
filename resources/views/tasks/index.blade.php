<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Webreinvent Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Webreinvent Task Manager</h2>

        <div class="row mb-3 justify-content-center">
            <div class="col-4 border p-2 rounded">
                <div class="input-group mb-3 ">
                    <input type="text" id="task-input" placeholder="Enter a task" class="form-control form-control-sm task-input">
                    <button id="add-task-btn" class="btn btn-primary btn-sm">Add Task</button>
                </div>
            </div>
        </div>

        <a href="/tasks" id="show-all-tasks" class="btn btn-secondary mb-3">Show All Tasks</a>

        <table class="table table-bordered task-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tasks-list">
                @foreach ($tasks as $task)
                    <tr data-id="{{ $task->id }}" class="{{ $task->complete ? 'completed' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $task->task }}</td>
                        <td>
                            {{ $task->complete ? 'done' : '' }}
                        </td>
                        <td>
                            <input type="checkbox" class="complete-task" data-id="{{ $task->id }}"
                                {{ $task->complete ? 'checked' : '' }}>
                            <button class="delete-task btn btn-danger btn-sm"
                                data-id="{{ $task->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <footer class="mt-4 text-center">
            <p class="mb-1">Developed by Vishal Kumar</p>
            <a href="https://www.linkedin.com/in/vishal-kumar-8882a0170/" target="_blank" class="btn btn-link">
                <i class="fab fa-linkedin"></i>
            </a>
            
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#add-task-btn').on('click', function() {
                let task = $('#task-input').val();
                if (task) {
                    $.ajax({
                        url: '/tasks',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            task: task
                        },
                        success: function(data) {
                            $('#task-input').val('');
                            appendTask(data);
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON.message);
                        }
                    });
                }
            });

            $(document).on('change', '.complete-task', function() {
                let taskId = $(this).data('id');
                let completed = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        complete: completed
                    },
                    success: function(data) {
                        if (completed) {
                            $(`tr[data-id="${taskId}"]`).addClass('completed').remove();
                        } else {
                            loadTasks();
                        }
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                if (confirm('Are you sure to delete this task?')) {
                    let taskId = $(this).data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            $(`tr[data-id="${taskId}"]`).remove();
                        }
                    });
                }
            });

            function loadTasks() {
                $('#tasks-list').empty();
                $.get('/tasks', function(data) {
                    data.forEach((task, index) => {
                        appendTask(task, index + 1); 
                    });
                });
            }

            function appendTask(task, index) {
                let taskHtml = `
                    <tr data-id="${task.id}" class="${task.complete ? 'completed' : ''}">
                        <td>${index}</td> <!-- Sr. No. or index -->
                        <td>${task.task}</td> <!-- Task description -->
                        <td>
                            ${task.complete ? 'done' : ''}
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="complete-task" data-id="${task.id}" ${task.complete ? 'checked' : ''}>
                                <span class="slider round"></span>
                            </label>
                            <button class="delete-task btn btn-danger btn-sm" data-id="${task.id}">Delete</button>
                        </td>
                    </tr>
                `;
                $('#tasks-list').append(taskHtml);
            }


        });
    </script>
</body>

</html>
