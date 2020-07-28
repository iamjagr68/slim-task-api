let datatable;

$(document).ready(function() {
    initDataTable();

    // Bind anchor click events to datatable as listener
    $('table')
        .on('click',    'a.edit',           onEditClick)
        .on('blur',     'input.edit-input', onEditComplete)
        .on('keypress', 'input.edit-input', onTaskKeyPress)
        .on('click',    'a.is_done',        onIsDoneClick)
        .on('click',    'a.delete',         onDeleteClick);
});

/**
 * Initialize the database
 */
function initDataTable() {
    // Custom datatable button definition
    $.fn.dataTable.ext.buttons.new = {
        action: promptForNewTask,
        className: 'new btn-sm btn-primary',
        init: function(api, node, config) {
            // remove btn-default class
            $(node).removeClass('btn-secondary');
            $(node).attr({ title: 'Add new task' });
        },
        text: 'New',
    };

    // Initialize DataTable
    datatable = $('table').DataTable({
        processing: true,
        buttons: [
            'new'
        ],
        dom: '<"row"<"col-sm-6"l><"col-sm-6"Bf>>rtip',
        ajax: {
            url: '/api/v1/tasks',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'task', render: function(data) {
                    let a        = `<a href="#" class="edit" title="Edit">${data}</a>`;
                    let input    = `<input class="edit-input form-control form-control-sm" maxlength="255" hidden />`;
                    let feedback = `<div class="invalid-feedback"></div>`;
                    return `${a}${input}${feedback}`;
                }},
            { data: 'is_done', render: function(data) {
                    let icon    = data && 'fa-check-square-o' || 'fa-square-o';
                    let tooltip = data && 'Un-complete' || 'Complete';
                    return `<a href="#" class="is_done" title="${tooltip}"><i class="fa fa-lg ${icon}"></i></a>`;
                }},
            { orderable: false, render: function() {
                    return `<a href="#" title="Delete" class="delete text-danger"><i class="fa fa-lg fa-trash"></i></a>`;
                }}
        ]
    });
}

/**
 * Fired off when clicking the Add button
 * to add a new task
 */
function promptForNewTask(e) {
    let task = $.trim(prompt('Enter new task'));

    // Confirm they entered a task to be added
    if (task && task.length > 0) {
        // Send request to API to create a new task
        $.ajax({
            type: 'POST',
            url: '/api/v1/tasks',
            dataType: 'json',
            data: {
                task
            },
            success: function (resp) {
                // Refresh datatable with ajax call
                datatable.ajax.reload(null, false);
            },
            error: function (xhr, status, err) {
                alert(xhr.responseJSON.message);
                promptForNewTask();
            }
        });
    }
}

/**
 * Fired off when a completed checkbox is clicked
 * to mark task as completed or not
 * @param e {Event} The event that triggered the function call
 */
function onIsDoneClick(e) {
    e.preventDefault();
    e.stopPropagation();

    // Check if we are completing/un-completing
    let row          = $(this).closest('tr');
    let isCompleting = $(this).find('i').hasClass('fa-square-o') ? 1 : 0;
    let rowData      = datatable.row(row).data();

    // Send off ajax API request to update task
    $.ajax({
        type: 'PUT',
        url: `/api/v1/tasks/${rowData.id}`,
        dataType: 'json',
        data: {
            ...rowData,
            is_done: isCompleting
        },
        success: function (res) {
            datatable.row(row).data(res).draw(false);
        }
    });
}

/**
 * Fired off when a task name is clicked
 * to enable editing of the task name
 * @param e {Event} The event that triggered the function call
 */
function onEditClick(e) {
    e.preventDefault();
    e.stopPropagation();

    // Get references to all inputs and data
    let a        = $(this);
    let input    = $(a).next('input.edit-input');
    let feedback = $(input).next('div.invalid-feedback');
    let row      = $(a).closest('tr');
    let rowData  = datatable.row(row).data();

    // Hide anchor
    $(a).prop({ hidden: true });
    // Show input and set value to be the task text
    $(input).val(rowData.task).prop({ hidden: false }).focus();
}

/**
 * Fired off when a task input is blurred
 * to save any change to the task name
 * @param e {Event} The event that triggered the function call
 */
function onEditComplete(e) {
    // Get references to all inputs and data
    let a        = $(this).prev('a.edit');
    let input    = $(a).next('input.edit-input');
    let feedback = $(input).next('div.invalid-feedback');
    let task     = $.trim($(input).val());
    let row      = $(a).closest('tr');
    let rowData  = datatable.row(row).data();

    // Test if input text has changed before making API request to update
    if (rowData.task !== task) {
        // Send off API request to update task
        $.ajax({
            type: 'PUT',
            url: `/api/v1/tasks/${rowData.id}`,
            dataType: 'json',
            data: {
                ...rowData,
                task
            },
            success: function (res) {
                // Reset input and feedback
                $(input).removeClass('is-invalid');
                $(feedback).empty();
                // Redraw row

                datatable.row(row).data(res).draw(false);
            },
            error: function (xhr, status, err) {
                // On error put contents of response.message in feedback
                $(feedback).text(xhr.responseJSON.message);
                // Add invalid class to input and reset task name to previous
                $(input).addClass('is-invalid').val(rowData.task).focus();
            }
        });
    } else {
        // Nothing was changed so revert row back to how it was
        $(a).prop({ hidden: false });
        $(input).prop({ hidden: true }).removeClass('is-invalid');
        $(feedback).empty();
    }
}

/**
 * Fired off when pressing return while editing a task name
 * to trigger the saving of the task
 * @param e {Event} The event that triggered the function call
 */
function onTaskKeyPress(e) {
    // If the enter key is pressed trigger blur to save
    if (e.keyCode === 13) {
        $(this).blur();
    }
}

/**
 * Fired off when the delete icon for a task is clicked
 * to delete the task
 * @param e {Event} The event that triggered the function call
 */
function onDeleteClick(e) {
    e.preventDefault();
    e.stopPropagation();

    // Get references to the row and data
    let row      = $(this).closest('tr');
    let rowData  = datatable.row(row).data();

    // Send off ajax API request to delete task
    $.ajax({
        type: 'DELETE',
        url: `/api/v1/tasks/${rowData.id}`,
        dataType: 'json',
        complete: function() {
            datatable.row(row).remove().draw(false);
        }
    });
}
