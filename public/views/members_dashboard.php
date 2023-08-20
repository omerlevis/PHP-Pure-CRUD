<!DOCTYPE html>
<html>
<head>
    <title>Music Band Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">

        <div class="card bg-light">
            <div class="card-body">
                <nav class="navbar">
                    <a href="/member" class="btn btn-secondary text-white ms-auto"> Add New Member</a>
                </nav>
                <table class="table table-bordered bg-white">
                    <tbody>

                    <tr>
                        <td>
                            <div class="card border border-white">
                                <div class="card-body text-center">

                                    <h1>Music Band Dashboard</h1>

                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#D8D8D8">
                            <div class="row no-gutters">
                                <div class="container">

                                    <div id="members"></div>

                                </div>
                            </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        //call to get all member using xhr and store the data in html table
        //add edit and delete buttons to the table according to the member id
        var membersDiv;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "/get-all-members", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var members;
                members = JSON.parse(xhr.responseText);
                membersDiv = document.getElementById("members");
                console.log(members);
                var tableHTML = '<table class="table table-bordered table striped">' +
                    '<tr>' +
                    '<th>ID Number</th>' +
                    '<th>First Name</th>' +
                    '<th>Last Name</th>' +
                    '<th>Email</th>' +
                    '<th>Band</th>' +
                    '<th>Action</th>' +
                    '</tr>';

                for (var i = 0; i < members.length; i++) {
                    tableHTML += '<tr>' +
                        '<td>' + members[i].id_number + '</td>' +
                        '<td>' + members[i].first_name + '</td>' +
                        '<td>' + members[i].last_name + '</td>' +
                        '<td>' + members[i].email + '</td>' +
                        '<td>' + members[i].band + '</td>' +
                        '<td> <a href="/member/' +members[i].id + '"class="btn btn-primary"> Edit</a> ' +
                        '<button class="btn btn-danger delete-button" data-id="' + members[i].id + '"> Delete</button></td>' +
                        '</tr>';
                }

                tableHTML += '</table>';
                membersDiv.innerHTML = tableHTML;
                // if there is error status code - display the error message instead the table
            } else {
                console.error(xhr.responseText);
                var erorr = JSON.parse(xhr.responseText);
                membersDiv = document.getElementById("members");
                membersDiv.innerHTML = 'Error: '+erorr['error'];

            }
        };
        xhr.send();

        //listen to the delete buttons in the table and if delete buttom clcked -
        //ask for confirm and call the deleteMember with the id of the member
        var membersContainer = document.getElementById('members');
        membersContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-button')) {
                var memberId = event.target.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this member?')) {
                    deleteMember(memberId, event.target.closest('tr'));
                }
            }
        });

        //call to delete member using xhr
        function deleteMember(memberId, memberRow) {
            var xhr = new XMLHttpRequest();
            xhr.open('DELETE', '/delete-member/' + memberId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    memberRow.remove(); // Remove the deleted member's row from the table
                } else {
                    console.error('Delete request failed. Status: ' + xhr.status);
                }
            };
            xhr.send();
        }
    });
</script>
</body>
</html>
