<!DOCTYPE html>
<html>
<head>
    <title>Add New Member</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<style>
    ::placeholder {
        font-size: 0.7em; 
    }
</style>

<body>
<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <nav class="navbar">
                <a href="/dashboard" class="btn btn-secondary text-white ms-auto"> Back to Dashboard</a>
            </nav>
            <?php if (json_encode($id) === 'null') : ?>
                <h1>Add New Member</h1>
            <?php else : ?>
                <h1>Edit Member</h1>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="addMemberForm">
                <label for="id_number" class="form-label">ID Number:</label>
                <input type="text" id="id_number" name="id_number" placeholder="Only digits(1-45 length)"  required pattern="[0-9]{1,45}" class="form-control"> <br>

                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" id="first_name" name="first_name" placeholder="Name max length:45" required pattern="[0-9a-zA-Z ]{1,45}" class="form-control"><br>

                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last name max length:45" required pattern="[0-9a-zA-Z ]{1,45}" class="form-control"><br>

                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" placeholder="Insert valid email address" required class="form-control"> <br>

                <label for="band" class="form-label">Band:</label>
                <input type="text" id="band" name="band" placeholder="Band max length:45" required pattern="[0-9a-zA-Z ]{1,45}" class="form-control"><br>

                <button type="submit" class="btn btn-primary">
                    <?php if (json_encode($id) === 'null') : ?>
                        Add Member
                    <?php else : ?>
                        Edit Member
                    <?php endif; ?>
                </button>
            </form>

            <div id="errorContainer" class="alert alert-danger" style="display: none;"></div>

        </div>
    </div>
</div>


<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalButton" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const memberId = <?php echo json_encode($id); ?>; //get the id from server
    const addMemberForm = document.getElementById('addMemberForm');
    //if id exist - call to get the member details and display in the form inputs
    document.addEventListener('DOMContentLoaded', () => {
        if (memberId) {
            fetch(`/get-member/${memberId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    document.getElementById('id_number').value = data.id_number;
                    document.getElementById('first_name').value = data.first_name;
                    document.getElementById('last_name').value = data.last_name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('band').value = data.band;
                })
                .catch(error => {
                    console.error('Error fetching member data:', error);
                    const errorContainer = document.getElementById('errorContainer');
                    errorContainer.style.display = 'block';
                    errorContainer.innerText = 'Error fetching member data';
                    addMemberForm.style.display = 'none';
                });
        }
    });

    //define request parameters for crate(post) or edit(put) member
    let formSubmitURL='/send-member';
    let formSubmitMethod ='POST';
    if (memberId) {
        formSubmitURL = formSubmitURL + '/' + memberId;
        formSubmitMethod='PUT';
    }

    //listen to the form submitting
    addMemberForm.addEventListener('submit', function (event) {
        event.preventDefault();
        //covert the form data to json format for allow the server to get both post
        //and put request , the json is sending by the BODY
        const formData = new FormData(addMemberForm);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });
        fetch(formSubmitURL, {
            method: formSubmitMethod,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
            .then(response => response.json())
            .then(data => {
                //crate a popup with bootstrap Moadal for display the action response
                const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                const modalTitle = document.getElementById('modalTitle');
                const modalBody = document.getElementById('modalBody');

                if (data.success) {
                    modalTitle.innerText = 'Success';
                    modalBody.innerText = data.message;
                    modal.show();
                    const closeModalButton = document.getElementById('closeModalButton');
                    closeModalButton.addEventListener('click', () => {
                        window.location.replace('/dashboard');
                    });
                } else {
                    modalTitle.innerText = 'Error';
                    modalBody.innerText = data.message;
                    modal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

</script>

</body>
</html>