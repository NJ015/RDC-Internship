$(function () {

    function save() {
        const rating = $('input[name="rating"]:checked').val();
        const userName = $('#userName').val();
        const feedbackText = $('#feedbackText').val();

        if (!rating || !userName || !feedbackText) {
            $('#modalErrorMsg').removeClass('d-none').text('Please fill in all fields.');
            console.error('Please fill in all fields.');
            return;
        }

        const feedback = {
            rating: rating,
            userName: userName,
            feedbackText: feedbackText
        }

        $.ajax({
            url: 'api/create.php',
            type: 'POST',
            data: JSON.stringify(feedback),
            contentType: 'application/json',
            success: function (response) {
                showToast('Feedback updated successfully!', 'success');
                console.log('Feedback saved successfully:', response);
                // add success alert
                $('#addFeedback').modal('hide');
                $('#userName').val('');
                $('#feedbackText').val('');
                $('input[name="rating"]').prop('checked', false);

                feedback.id = response.id;
                $('#feedbackContainer').prepend(getFeedbackCard(feedback));
            },
            error: function (xhr, status, error) {
                showToast('Error editing feedback. Please try again.', 'danger');
                $('#modalErrorMsg').removeClass('d-none').text('Error saving feedback. Please try again.');
            }
        })
    }

    function getFeedbackCard(feedback) {
        // add an id to diff: `#feedback-${id}`
        // this matches the delete
        const feedbackCard = $('<div>').addClass('card bg-light shadow-sm feedback').attr({
            id: `feedback-${feedback.id}`,
            role: 'button',
            tabindex: '0',
            'data-bs-toggle': 'modal',
            'data-bs-target': '#viewFeedback'
        })

        const topDiv = $('<div>').addClass('mb-2');

        const nameDiv = $('<div>').addClass('text-muted fs-5').text(feedback.userName);
        // nameDiv.id = 'name';

        const ratingDiv = $('<div>').addClass('rating').text('★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating));

        const textDiv = $('<div>').attr('id', 'textf').addClass('mt-1').text(feedback.feedbackText);

        topDiv.append(nameDiv);
        topDiv.append(ratingDiv);

        feedbackCard.append(topDiv);
        feedbackCard.append(textDiv);

        feedbackCard.data('feedback', feedback); // Store feedback data in the card
        feedbackCard.on('click', function () {
            getModalDetails($(this).data('feedback'));
        });


        return feedbackCard;
        // <div id="feedback" class="bg-light" role="button" tabindex="0" data-bs-toggle="modal"
        //         data-bs-target="#viewFeedback">
        //         <div class="mb-2">
        //             <div id="name" class="text-muted fs-5">Name</div>
        //             <div class="rating">
        //                 <span>★★★★☆</span>
        //             </div>
        //         </div>

        //         <div id="textf" class="mt-1">Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
        //             Ipsum Lorem
        //             Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
        //             Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
        //             Ipsum Lorem Ipsum</div>
        //     </div>
    }

    function getFeedbacks() {
        $.ajax({
            url: 'api/get.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Feedbacks retrieved successfully:', response);
                // add success alert
                $('#feedbackContainer').empty(); // Clear existing feedbacks
                if (response.length === 0) {
                    $('#feedbackContainer').append('<div class="text-center text-muted">No feedbacks available.</div>');
                    return;
                }
                response.forEach(feedback => {
                    $('#feedbackContainer').append(getFeedbackCard(feedback));
                });
            },
            error: function (xhr, status, error) {
                showToast('Error retrieving feedbacks.', 'danger');
                // console.error('Error retrieving feedbacks:', error);
                // add error alert
            }
        })
    }

    getFeedbacks();

    function deleteFeedback(id) {
        $.ajax({
            url: 'api/delete.php',
            type: 'POST',
            data: JSON.stringify({ id: id }),
            contentType: 'application/json',
            success: function (response) {
                showToast('Feedback deleted successfully!', 'success');
                console.log('Feedback deleted successfully:', response);
                // add success alert
                $(`#feedback-${id}`).remove(); // Remove the feedback card from the DOM
            },
            error: function (xhr, status, error) {
                showToast('Error deleting feedback. Please try again.', 'danger');
                console.error('Error deleting feedback:', error);
                // add error alert
            }
        })
    }

    function editFeedback(id, updatedFeedback) {
        $.ajax({
            url: 'api/update.php',
            type: 'POST',
            data: JSON.stringify({ id: id, feedback: updatedFeedback }),
            contentType: 'application/json',
            success: function (response) {
                showToast('Feedback updated successfully!', 'success');
                console.log('Feedback edited successfully:', response);
                // add success alert
                $(`#feedback-${id}`).replaceWith(getFeedbackCard({ ...updatedFeedback, id }));
                $('#addFeedback').modal('hide');
                $('#savebtn').text('Save').off('click').on('click', function (e) {
                    e.preventDefault();
                    save();
                });
            },
            error: function (xhr, status, error) {
                showToast('Error editing feedback. Please try again.', 'danger');
                $('#modalErrorMsg').removeClass('d-none').text('Error editing feedback. Please try again.');
            }
        })
    }

    function getModalDetails(feedback) {
        // This function should populate the modal with feedback details
        // and set up the delete and edit buttons.
        // $('#viewFeedback .modal-title').text(`Feedback from ${feedback.userName}`);
        $('#viewFeedback #nameModal').text(feedback.userName);
        $('#viewFeedback #feedbackTextModal').text(feedback.feedbackText);
        $('#viewFeedback .rating span').text('★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating));
        $('#viewFeedback').data('feedback-id', feedback.id);
        $('#viewFeedback').data('rating', feedback.rating); // Store feedback data in the modal
    }


    $('#savebtn').on('click', function (e) {
        e.preventDefault();
        save();
    });

    $('#deletebtn').on('click', function (e) {
        e.preventDefault();
        const id = $('#viewFeedback').data('feedback-id');
        if (confirm('Are you sure you want to delete this feedback?')) {
            deleteFeedback(id);
        }
    });

    $('#editbtn').on('click', function (e) {
        e.preventDefault();

        const id = $('#viewFeedback').data('feedback-id');

        $('#userName').val($('#viewFeedback #nameModal').text());
        $('#feedbackText').val($('#viewFeedback #feedbackTextModal').text());
        $(`input[name="rating"][value=${$('#viewFeedback').data('rating')}]`).prop('checked', true);

        $('#viewFeedback').modal('hide'); // Hide the modal before editing
        $('#addFeedback').modal('show'); // Show the edit modal

        $('#savebtn').text('Update').off('click').on('click', function (e) {
            e.preventDefault();

            const updatedFeedback = {
                rating: $('input[name="rating"]:checked').val(),
                userName: $('#userName').val(),
                feedbackText: $('#feedbackText').val()
            };

            if (!updatedFeedback.rating || !updatedFeedback.userName || !updatedFeedback.feedbackText) {
                $('#modalErrorMsg').removeClass('d-none').text('Please fill in all fields.');
                console.error('Please fill in all fields.');
                return;
            }

            // console.log('Updating feedback:', updatedFeedback);
            // console.log('Feedback ID:', id);
            editFeedback(id, updatedFeedback);
        });


    });

    $('#addFeedback').on('hidden.bs.modal', function () {
        $('#savebtn').text('Submit Feedback').off('click').on('click', function (e) {
            e.preventDefault();
            save();
        });
        $('#userName').val('');
        $('#feedbackText').val('');
        $('input[name="rating"]').prop('checked', false);
        $('#modalErrorMsg').addClass('d-none').text('');
    });



    if (isAdmin) {
        $('#viewFeedback .modal-footer').removeClass('d-none');
        $('footer').text('An admin? ')
        const logoutLink = $('<a>').attr('href', 'api/logout.php').text('Logout');
        $('footer').append(logoutLink);
    }
    else {
        $('#viewFeedback .modal-footer').addClass('d-none');
        $('footer').text('An admin? ')
        const loginLink = $('<a>').attr('href', 'login.php').text('Login');
        $('footer').append(loginLink);
    }

    function showToast(message, type) {
        const toast = $('#mainToast');
        toast.removeClass('text-bg-danger text-bg-success text-bg-primary text-bg-warning').addClass('text-bg-' + type);
        $('#mainToastMsg').text(message);
        const toastInstance = bootstrap.Toast.getOrCreateInstance(document.getElementById('mainToast'));
        toastInstance.show();
    }

});

// TODO: add error handling for backend errors
// TODO: add confirmation for delete
// TODO: delete confirmation, error handling/showing, feedback toasts