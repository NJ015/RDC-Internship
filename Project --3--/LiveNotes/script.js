$(function () {

    let currentSort = "created_desc"; // used to keep track of sort when reloading notes

    // Helper to escape HTML (to prevent XSS)
    function escapeHtml(text) {
        return text.replace(/[&<>"']/g, function (m) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[m];
        });
    }

    // used in noteForm because content is inserted 
    // as input value not as html so it needs not be escaped
    function unescapeHtml(text) {
        return text
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&amp;/g, "&")
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'");
    }

    function loadNotes(query = "", sort = "created_desc") {
        $.ajax({
            url: "notes.php",
            type: "GET",
            data: { search: query, sort: sort },
            dataType: "json",
            success: function (notes) {
                let notesHtml = "";
                if (notes.length == 0) {
                    notesHtml =
                        '<div class="alert alert-info w-100">No notes found.</div>';
                } else {
                    // only replaces the first <br>
                    notes.forEach(function (note) {
                        // if pinned, add pinned class
                        let pinnedClass = note.pinned == 1 ? "pinned" : "";
                        notesHtml += `
                            <div class="card note ${pinnedClass}" data-id="${note.id}" data-pinned="${note.pinned}">
                                <h4 class="card-title">${escapeHtml(note.title)}</h4>
                                <div class="card-body">
                                    <p class="card-text">${escapeHtml(note.content).replace(/\n/g, "<br>")}</p>
                                    <div class="note-actions">
                                        <button class="btn btn-secondary pin-note" title="Pin/Unpin" data-bs-toggle="tooltip"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn btn-warning share-note" title="Share" data-bs-toggle="tooltip"><i class="fas fa-share"></i></button>
                                        <button class="btn btn-danger delete-note" title="Delete" data-bs-toggle="tooltip"><i class="fas fa-trash-can"></i></button>
                                    </div>
                                </div>
                            </div>
                            `;
                    });
                }
                reinitTooltips()
                $(".notes-list").html(notesHtml);
                reinitTooltips();
            },
            error: function () {
                showToast("Error loading notes.", "error");
            },
        });
    }

    loadNotes();

    $(".notes-list").on("click", '.note', function () {
        const noteId = $(this).data("id");
        const title = unescapeHtml($(this).find(".card-title").html());
        const content = unescapeHtml($(this).find(".card-text").html().replace(/<br\s*\/?>/gi, "\n"));
        const pinned = $(this).data('pinned');
        $("#title").val(title);
        $("#content").val(content);
        $("#noteId").val(noteId);
        $('#notePinned').val(pinned);
        updatePinButton(!pinned)
        $("#noteForm").find('button[type="submit"]').text("Update Note");
    });

    // Add or Update note
    $("#noteForm").on("submit", function (e) {
        e.preventDefault();
        const title = $("#title").val();
        const content = $("#content").val();
        const noteId = $("#noteId").val();

        if (!title.trim() || !content.trim()) {
            showToast("Title and content cannot be empty or just spaces.", "error");
            return;
        }

        if (title.length > 100) {
            showToast("Title is too long (max 100 characters).", "error");
            return;
        }

        let postData = { title: title, content: content };
        if (noteId) postData.id = noteId;

        $.ajax({
            url: "notes.php",
            type: "POST",
            data: postData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    loadNotes();
                    // comment if u want to keep the form data after submission
                    $("#noteForm")[0].reset();
                    $("#noteId").val("");
                    $("#noteForm").find('button[type="submit"]').text("Add Note");
                    showToast("Note saved successfully.", "success");

                } else {
                    showToast("Error saving note.", "error");
                }
            },
            error: function (xhr, status, error) {
                showToast("Error submitting. Please try again.", "error");
            }
        });
    });


    // delete note from note-list
    $(".notes-list").on("click", ".btn-danger", function (e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to delete this note?")) return;
        const noteCard = $(this).closest(".note");
        const noteId = noteCard.data("id");
        $.ajax({
            url: "notes.php",
            type: "POST",
            data: { id: noteId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#noteForm")[0].reset();
                    $("#noteId").val("");
                    $("#noteForm").find('button[type="submit"]').text("Add Note");

                    $(`.note[data-id="${noteId}"]`).remove();

                    showToast("Note deleted successfully.", "success");
                } else {
                    showToast("Error deleting note.", "error");
                }
            },
            error: function (xhr, status, error) {
                showToast("Error while deleting. Please try again.", "error");
            }
        });
    });

    // Delete note from form
    $("#noteForm").on("click", ".delete-note", function (e) {
        e.preventDefault();
        const noteId = $("#noteId").val();
        if (!noteId) return;
        if (!confirm("Delete this note?")) return;
        $.ajax({
            url: "notes.php",
            type: "POST",
            data: { id: noteId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#noteForm")[0].reset();
                    $("#noteId").val("");
                    $("#noteForm").find('button[type="submit"]').text("Add Note");

                    // remove THE card


                    showToast("Note deleted successfully.", "success");
                } else {
                    showToast("Error deleting note.", "error");
                }
            },

            error: function (xhr, status, error) {
                showToast("Error while deleting. Please try again.", "error");
            }
        });
    });


    // Clear form
    $("#noteForm").on("click", ".clear-note", function () {
        $("#noteForm")[0].reset();
        $("#noteId").val(""); $("#notePinned").val("0");
        updatePinButton(0);
        $("#noteForm").find('button[type="submit"]').text("Add Note");
    });

    // check whether its better to have the checking ///////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    // done from the existing cards rather than from db side
    // Live search as you type
    $(".search-bar").on("input", function () {
        const searchQuery = $("#searchQuery").val();
        loadNotes(searchQuery);
    });

    // Prevent form submit from reloading the page
    $(".search-bar").on("submit", function (e) {
        e.preventDefault();
        const searchQuery = $("#searchQuery").val();
        loadNotes(searchQuery);
    });

    // Reset to default sort
    $(".sort").on("click", ".sort-reset", function () {
        loadNotes($("#searchQuery").val(), "created_desc");
    });

    // Sort by selected option
    $(".sort").on("click", ".sort-option", function () {
        const sort = $(this).data("sort");
        loadNotes($("#searchQuery").val(), sort);
    });

    // Dark mode toggle
    $("#dark-mode").on("click", function () {
        $('body').attr('data-bs-theme', function (i, val) {
            return val === 'dark' ? 'light' : 'dark';
        });

        $(this).find("i").toggleClass("fa-sun fa-moon");

        const isDark = $('body').attr('data-bs-theme') === 'dark';
        const newTitle = isDark ? 'Light mode' : 'Dark mode';
        $(this).attr('title', newTitle);

        // Update tooltip content (Bootstrap 5.2+)
        const tooltipInstance = bootstrap.Tooltip.getInstance(this);
        if (tooltipInstance) {
            tooltipInstance.setContent({ '.tooltip-inner': newTitle });
        }

        $('.btn').each(function () {
            const $btn = $(this);
            // List of Bootstrap color classes
            const colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
            colors.forEach(color => {
                if ($btn.hasClass('btn-' + color)) {
                    $btn.removeClass('btn-' + color).addClass('btn-outline-' + color);
                } else if ($btn.hasClass('btn-outline-' + color)) {
                    $btn.removeClass('btn-outline-' + color).addClass('btn-' + color);
                }
            });
        });
    });

    // pin note
    $(".notes-list").on("click", ".pin-note", function () {
        const noteCard = $(this).closest(".note");
        const noteId = noteCard.data("id");
        const pinState = noteCard.hasClass("pinned") ? 0 : 1;

        $.ajax({
            url: "notes.php",
            type: "POST",
            data: { pin_id: noteId, status: pinState },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    loadNotes($("#searchQuery").val());
                    if (pinState == 0)
                        showToast("Note unpinned successfully.", "success");
                    else
                        showToast("Note pinned successfully.", "success");
                } else {
                    showToast("Error pinning note.", "error");
                }
            },

            error: function (xhr, status, error) {
                showToast("Error while pinning. Please try again.", "error");
            }
        });
    });

    $('#noteForm').on('click', '.pin-note', function () {
        const noteId = $('#noteId').val();
        if (!noteId) {
            showToast("Please select a note.", "error");
            return;
        }

        let pinState = $("#notePinned").val() == "1" ? 0 : 1;

        $.ajax({
            url: "notes.php",
            type: "POST",
            data: { pin_id: noteId, status: pinState },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#notePinned").val(pinState);
                    updatePinButton(pinState);
                    loadNotes($("#searchQuery").val());
                } else {
                    showToast("Error pinning note.", "error");
                }
            },

            error: function (xhr, status, error) {
                showToast("Error while pinning. Please try again.", "error");
            }
        });
    });

    function updatePinButton(pinned) {
        const $btn = $("#formPinBtn");
        $btn.removeClass("btn-info btn-secondary btn-outline-info btn-outline-secondary");
        if (pinned == 1) {
            if ($('body').attr('data-bs-theme') == 'dark') {
                $btn.addClass("btn-outline-info");
            } else {
                $btn.addClass("btn-info");
            }
            $btn.attr("title", "Unpin");
        } else {
            if ($('body').attr('data-bs-theme') == 'dark') {
                $btn.addClass("btn-outline-secondary");
            } else {
                $btn.addClass("btn-secondary");
            }
            $btn.attr("title", "Pin");
        }
    }

    // Share note
    $(".notes-list").on("click", ".share-note", function () {
        const noteCard = $(this).closest(".note");
        const title = noteCard.find(".card-title").text();
        const content = noteCard
            .find(".card-text")
            .html()
            .replace(/<br\s*\/?>/gi, "\n"); // replace <br> with \n
        const text = `Title: ${title}\nContent: ${content}`;
        /* The navigator object is a built-in JavaScript object 
            that gives you info about the browser and device.
            navigator.clipboard is a modern API that lets you 
            read from or write to the clipboard (the thing you copy/paste from). */
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {

                showToast("Note copied to clipboard.", "success");

            });
        } else {
            showToast("Clipboard not supported.", "warning");

        }
    });

    $("#noteForm").on("click", ".share-note", function () {
        const title = $('#title').val();
        const content = $('#content').val().replace(/<br\s*\/?>/gi, "\n"); // replace <br> with \n
        const text = `Title: ${title}\nContent: ${content}`;
        console.log(text)
        /* The navigator object is a built-in JavaScript object 
            that gives you info about the browser and device.
            navigator.clipboard is a modern API that lets you 
            read from or write to the clipboard (the thing you copy/paste from). */
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                showToast("Note copied to clipboard.", "success");
            });
        } else {
            showToast("Clipboard not supported.", "warning");
        }
    });

    // Print note
    $("#noteForm").on("click", ".print-note", function () {
        const title = $("#title").val();
        const content = $("#content").val().replace(/\n/g, "<br>");
        const theme = $('body').attr('data-bs-theme');
        $('body').attr('data-bs-theme', 'light');
        // <div class="row main-content h-100">
        // <div class="col-12 d-flex flex-wrap justify-content-center align-content-center">
        $("#printArea").html(`
                                <!-- <div class="card note"> -->
                                    <h4 class="card-title">${title}</h4>
                                    <div class="card-body">
                                        <p class="card-text">${content}</p> 
                                        </p>
                                        <!-- <div class="note-actions">
                                            <button class="btn btn-primary edit">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-danger delete-note">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                            <button class="btn btn-secondary pin-note">
                                                <i class="fas fa-thumbtack"></i>
                                            </button>
                                            <button class="btn btn-warning share-note">
                                                <i class="fa-solid fa-share"></i>
                                            </button>
                                        </div> -->
                                    </div>
                                <!-- </div> -->
                            
                            `);

        window.print();
        setTimeout(() => {
            $("#printArea").html(""); // Clear print area after printing
        });
        $('body').attr('data-bs-theme', theme); // Restore theme
    });




    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
    function reinitTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
    }


    function showToast(message, type = "success") {
        const $toast = $("#liveToast");
        $toast.removeClass("text-bg-success text-bg-danger text-bg-primary text-bg-warning");
        $toast.addClass("text-bg-" + (type === "error" ? "danger" : type));
        $toast.find(".toast-body").html(message);
        const toast = new bootstrap.Toast($toast[0], { delay: 3000 });
        toast.show();
    }

});
