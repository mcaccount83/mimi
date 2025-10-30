<script>
    function confirmActivateSingleBoard() {
        Swal.fire({
            title: 'Activate Board?',
            text: 'Are you sure you want to activate this board?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Activate!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('activateSingleBoardForm').submit();
            }
        });
    }

    function confirmActivateAllBoards() {
        Swal.fire({
            title: 'Activate All Boards?',
            html: 'This action will activate all received boards.<br><br>Are you sure you want to activate all received boards?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Activate All!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('activateAllBoardsForm').submit();
            }
        });
    }

</script>
