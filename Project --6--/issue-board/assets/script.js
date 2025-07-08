$(function () {

    // TODO: i set the default options in selectors to val='-1' ==> need to handle it in dismiss modal and validations (if(empty)==>if(val<0))

    function renderCard(issue) {

        const status = Number(issue.status);
        const targetCol = $(`#status_${status} .cardsContainer`);

        const priority_name = getPriorityName(issue.priority);

        const details = {
            id: issue.id,
            title: issue.title,
            description: issue.description,
            project_id: issue.project_id,
            project_name: issue.project_name,
            priority: issue.priority,
            created_at: issue.created_at,
            status: status
        };

        // <div class="card issue-item" data-bs-toggle="modal" data-bs-target="#detailsModal">
        //     <div class="issue-title card-header">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
        //     <div class="card-body d-flex flex-column gap-1 align-items-start">
        //         <div class="mb-3">
        //             <span class="issue-description text-muted">Description of issue 1</span>
        //         </div>
        //         <div>
        //             <span class="fw-bold">Project: </span>
        //             <span id="project">Project Name</span>
        //         </div>
        //         <div>
        //             <span class="fw-bold">Priority: </span>
        //             <span id="project">High</span>
        //         </div>
        //     </div>

        //     <div class="issue-actions d-flex justify-content-between gap-1 card-footer">
        //         <button id="b4btn" class="btn btn-outline-warning"><i class="fas fa-arrow-left"></i></button>
        //         <button class="btn btn-outline-info"><i class="fas fa-pen"></i></button>
        //         <button class="btn btn-outline-danger"><i class="fas fa-trash-can"></i></button>
        //         <button id="nextbtn" class="btn btn-outline-success"><i class="fas fa-arrow-right"></i></button>
        //     </div>
        // </div>

        let issueDiv = $('<div>').addClass('card issue-item').data('issue_details', details).attr('data-id', issue.id);

        // title

        let titleDiv = $('<div>').addClass('issue-title card-header').text(issue.title).attr('data-bs-toggle', 'modal').attr('data-bs-target', '#detailsModal').on('click', function () {
            displayDetails(details);
        });

        // title


        // body

        let bodyDiv = $('<div>').addClass('card-body d-flex flex-column gap-1 align-items-start').attr('data-bs-toggle', 'modal').attr('data-bs-target', '#detailsModal').on('click', function () {
            displayDetails(details);
        });

        let descriptionDiv = $('<div>').addClass('mb-3');

        let descriptionSpan = $('<span>').addClass('issue-description text-muted').text(issue.description);

        descriptionDiv.append(descriptionSpan);
        bodyDiv.append(descriptionDiv);

        let projectDiv = $('<div>');
        let projectSpan = $('<span>').addClass('fw-bold').text('Project: ');
        let projectNameSpan = $('<span>').text(issue.project_name);

        projectDiv.append(projectSpan).append(projectNameSpan);
        bodyDiv.append(projectDiv);

        let priorityDiv = $('<div>');
        let prioritySpan = $('<span>').addClass('fw-bold').text('Priority: ');

        let priorityNameSpan = $('<span>').text(priority_name);

        priorityDiv.append(prioritySpan).append(priorityNameSpan);
        bodyDiv.append(priorityDiv);

        // body



        // actions

        let actionsDiv = $('<div>').addClass('issue-actions d-flex justify-content-between gap-1 card-footer');

        // let b4btn = $('<button>').attr('id', 'b4btn').addClass('btn btn-outline-warning').html('<i class="fas fa-arrow-left"></i>');

        // let nextbtn = $('<button>').attr('id', 'nextbtn').addClass('btn btn-outline-success').html('<i class="fas fa-arrow-right"></i>');

        let b4btn = $('<button>', {
            id: 'b4btn_' + issue.id,
            type: 'button',
            class: 'btn btn-outline-warning',
            html: '<i class="fas fa-arrow-left"></i>',
            disabled: status === 0
        }).on('click', function () {
            if (!$(this).prop('disabled')) {
                const newStatus = status - 1;
                updateStatus(issue.id, newStatus);
            }
        });

        let nextbtn = $('<button>', {
            id: 'nextbtn_' + issue.id,
            type: 'button',
            class: 'btn btn-outline-success',
            html: '<i class="fas fa-arrow-right"></i>',
            disabled: status === 3
        }).on('click', function () {
            if (!$(this).prop('disabled')) {
                const newStatus = status + 1;
                updateStatus(issue.id, newStatus);
            }
        });

        let editbtn = $('<button>').addClass('btn btn-outline-info').html('<i class="fas fa-pen"></i>').on('click', function () {
            $('#addIssueModal').modal('show');
            $('#addIssueModal').find('.modal-title').text('Edit Issue');
            $('#issueTitle').val(issue.title);
            $('#issueDesc').val(issue.description);

            populateProjects();

            setTimeout(() => {
                $('#issueProject').val(issue.project_id);
            }, 100);

            // $('#issueProject').val(issue.project_id);
            $('#issuePriority').val(issue.priority);
            console.log(issue.priority);
            console.log($('#issuePriority').val());
            $('#issueStatus').val(issue.status);
            $('#issueId').val(issue.id);
            $('#addIssueBtn').off('click').on('click', function () {
                updateIssue(issue.id)
            });
            $('#addIssueBtn').text('Update Issue');
        });

        let deletebtn = $('<button>').addClass('btn btn-outline-danger').html('<i class="fas fa-trash-can"></i>').on('click', function (e) {
            if (confirm('Are you sure you want to delete this issue?')) {
                deleteIssue(issue.id);
            }
        });

        actionsDiv.append(b4btn).append(editbtn).append(deletebtn).append(nextbtn);

        // actions


        issueDiv.append(titleDiv).append(bodyDiv).append(actionsDiv);

        targetCol.prepend(issueDiv);

    }

    function getPriorityName(priority) {
        return ['Low', 'Medium', 'High', 'Critical'][priority] || 'Unknown';
    }

    function getStatusName(status) {
        return ['To Do', 'In Progress', 'In Review', 'Done'][status] || 'Unknown';
    }


    function getAll() {
        $.ajax({
            url: 'api/get.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {

                $('.cardsContainer').empty();

                data.forEach(issue => {
                    renderCard(issue);
                });
            }
        });

    }

    getAll();

    function add() {
        const title = $('#issueTitle').val();
        const description = $('#issueDesc').val();
        const project_id = $('#issueProject').val();
        const priority = $('#issuePriority').val();
        const status = $('#issueStatus').val();

        if (title === '' || description === '' || project_id === '' || priority === '' || status === '') {
            alert('Please fill all fields');
            return;
        }

        $.ajax({
            url: 'api/create.php',
            type: 'POST',
            data: {
                title: title,
                description: description,
                project_id: project_id,
                priority: priority,
                status: status
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#addIssueModal').modal('hide');
                    renderCard(data.issue);
                } else {
                    alert('Error adding issue');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while adding the issue.');
            }
        });
    }

    $('#addIssueBtn').on('click', add);

    function populateProjects() {
        $.ajax({
            url: 'api/get_projects.php',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                const projectSelect = $('#issueProject');
                projectSelect.empty();
                projectSelect.append('<option value="">Select Project</option>');
                data.projects.forEach(project => {
                    projectSelect.append(`<option value="${project.id}">${project.name}</option>`);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while fetching projects.');
            }
        });
    }

    $('#addIssueModal').on('show.bs.modal', function () {
        populateProjects();
        $('#issueTitle').val('');
        $('#issueDesc').val('');
        $('#issueProject').val('-1');
        $('#issuePriority').val('-1');
        $('#issueStatus').val('-1');
    });
    $('#addIssueModal').on('hidden.bs.modal', function () {
        $('#addIssueBtn').text('Add Issue');
        $('#addIssueBtn').off('click').on('click', add);
        $('#issueId').val(''); // Reset issue id
        $('#issueTitle').val('');
        $('#issueDesc').val('');
        $('#issueProject').val('-1');
        $('#issuePriority').val('-1');
        $('#issueStatus').val('-1');
    });



    function deleteIssue(issueId) {
        $.ajax({
            url: 'api/delete.php',
            type: 'POST',
            data: { id: issueId },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $(`.issue-item[data-id='${issueId}']`).remove();

                } else {
                    alert('Error deleting issue');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the issue.');
            }
        });
    }

    function updateIssue(issueId) {
        const title = $(`#issueTitle`).val();
        const description = $(`#issueDesc`).val();
        const project_id = $(`#issueProject`).val();
        const priority = $(`#issuePriority`).val();
        const status = $(`#issueStatus`).val();

        const updatedData = {
            title: title,
            description: description,
            project_id: project_id,
            priority: priority,
            status: status

        };

        $.ajax({
            url: 'api/edit.php',
            type: 'POST',
            data: { id: issueId, ...updatedData },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    const issue = data.issue;
                    $(`.issue-item[data-id='${issueId}']`).remove();
                    renderCard(issue);
                    $('#addIssueModal').modal('hide');
                } else {
                    alert('Error updating issue');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while updating the issue.');
            }
        });
    }


    function updateStatus(issueId, newStatus) {
        $.ajax({
            url: 'api/update_status.php',
            type: 'POST',
            data: { id: issueId, status: newStatus },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $(`.issue-item[data-id='${issueId}']`).remove();
                    renderCard(data.issue);
                } else {
                    alert('Error updating issue status');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while updating the issue status.');
            }
        });
    }

    // const details = {
    //         id: issue.id,
    //         title: issue.title,
    //         description: issue.description,
    //         project_id: issue.project_id,
    //         project_name: issue.project_name,
    //         priority: issue.priority,
    //         created_at: issue.created_at,
    //         status: status
    //     };

    function displayDetails(data) {
        $('#priorityM').text(getPriorityName(data.priority));
        $('#statusM').text(getStatusName(data.status));

        $('#title').text(data.title);
        $('#description').text(data.description);

        $('#projectM').text(data.project_name);
        //  $('#createdBy').text(data.created_by);
        $('#createdOn').text(data.created_at);

        // Clear previous assignees
        $('#assignees').empty();

        // Get and render assignees
        getAssignees(data.id);

        $('#addAssignee').on('click', function () {
            loadAssigneeList(data.id)
        });
        $('#assigneeList').attr("issue-id", data.id);
        console.log('Assigning to issue:', $('#assigneeList').attr('issue-id')); // here its there

    }

    function getAssignees(issue_id){
        $.ajax({
            url: 'api/get_assignees.php',
            type: 'GET',
            data: { id: issue_id },
            dataType: 'json',
            success: function (results) {
                $('#assignees').empty();
                results.assignees.forEach(assignee => {
                    renderAssigneeCard(assignee, issue_id)
                    loadAssigneeList(issue_id);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while fetching the assignees.');
            }
        });
    }

    function renderAssigneeCard(assignee, issue_id) {

        const h6Div = $('<h6>').addClass('m-0');
        const nameSpan = $('<span>').addClass('badge bg-secondary d-flex align-items-center').text(assignee.username);
        const vrdiv = $('<span>').addClass('vr ms-1');
        const xbtn = $('<button>', {
            type: 'button',
            class: 'btn-close btn-close-white assigneeXBtn'
        }).on('click', function () {
            const userId = assignee.id;
            const issueId = issue_id;

            $.ajax({
                url: 'api/unassign.php',
                type: 'POST',
                data: { issue_id: issueId, user_id: userId },
                dataType: 'json',
                success: function () {
                    console.log("issue: ", issueId, "\nuser: ", userId);
                    h6Div.remove(); // $(this).parent().parent().remove();
                },
                error: function () {
                    alert('Failed to unassign user.');
                }
            });
        });

        nameSpan.append(vrdiv).append(xbtn);
        h6Div.append(nameSpan);

        $('#assignees').append(h6Div);

    }

    function loadAssigneeList(issueId) {
        $.ajax({
            url: 'api/get_available_users.php',
            type: 'GET',
            data: { issue_id: issueId },
            dataType: 'json',
            success: function (users) {
                const listDiv = $('#assigneeList');
                listDiv.empty();

                users.forEach(user => {
                    const id = `assignee-${user.id}`;

                    const checkbox = $(`
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="assignee_${id}" value="${user.id}">
                        <label class="form-check-label" for="assignee_${id}">${user.username}</label>
                    </div>
                `);

                    listDiv.append(checkbox);
                });
            },
            error: function () {
                alert('Failed to load users.');
            }
        });
    }

    $('#assign').on('click', function () {
        const issueId = $('#assigneeList').attr("issue-id"); //here its undefined
        console.log(issueId);
        const selectedUserIds = $('#assigneeList input:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedUserIds.length === 0) return;

        $.ajax({
            url: 'api/assign.php',
            type: 'POST',
            data: {
                issue_id: issueId,
                user_ids: selectedUserIds
            },
            traditional: true,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    // $('#detailsModal').modal('hide');
                    alert('Users assigned!');
                    getAssignees(issueId);
                    // optionally reload assignees view or call displayDetails again
                } else {
                    alert(res.message || 'Failed to assign users.');
                }
            },
            error: function (xhr, status, error) {
                alert('Error assigning users.');
                alert(error);
            }
        });
    });


})


/////////////////////////////////////////////
// multiple selects for assignees not working
/////////////////////////////////////////////
