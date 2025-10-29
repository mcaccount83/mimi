<script>
    function showPositionInformation() {
        Swal.fire({
            title: '<strong>Position Information</strong>',
            html: `
                <h4>Display Position</h4>
                <p>The Display Position will be used in areas that are publically visible. Examples: MIMI chapter screens, emails, pdf letters, forum signature, etc.</p>
                <br>
                <h4>MIMI Position</h4>
                <p>The MIMI Position is used for chapter hierarchy/level purposes and is required for anyone who oversees chapters. Even if this is not their role title, one of
                    these needs to be selected for MIMI to function properly.</p>
                <br>
                <h4>Secondary Positions</h4>
                <p>Multiple Secondary Positions may be chosen. Secondary Posistions may allow additional access outside of normal chapter/coordinator menus/screens based on the
                    job requirements while others may be for information/visual purposes only and will not affect MIMI interaction.</p>
                `,
            focusConfirm: false,
            confirmButtonText: 'Close',
            customClass: {
                popup: 'swal-wide',
                confirmButton: 'btn btn-danger'
            }
        });
    }

    function showPositionAbbreviations() {
        Swal.fire({
            title: '<strong>Position Abbreviations</strong>',
            html: `
            <h4><strong>Conference Positions</h4></strong>
                <table>
                    <tr><td><h4>BS</h4></td><td><h4>Big Sister</h4></td></tr>
                    <tr><td><h4>AC</h4></td><td><h4>Area Coordinator</h4></td></tr>
                    <tr><td><h4>SC</h4></td><td><h4>State Coordinator</h4></td></tr>
                    <tr><td><h4>ARC</h4></td><td><h4>Assistant Regional Coordinator</h4></td></tr>
                    <tr><td><h4>RC</h4></td><td><h4>Regional Coordinator</h4></td></tr>
                    <tr><td><h4>ACC&nbsp;&nbsp;&nbsp;&nbsp;</h4></td><td><h4>Assistant Conference Coordinator</h4></td></tr>
                    <tr><td><h4>CC</h4></td><td><h4>Conference Coordinator</h4></td></tr>
                    <tr><td><h4>IC</h4></td><td><h4>Inquiries Coordinator</h4></td></tr>
                    <tr><td><h4>WR</h4></td><td><h4>Website Reviewer</h4></td></tr>
                    <tr><td><h4>CDC</h4></td><td><h4>Chapter Development Coordinator</h4></td></tr>
                    <tr><td><h4>SPC</h4></td><td><h4>Special Projects Coordinator</h4></td></tr>
                    <tr><td><h4>BSM</h4></td><td><h4>Big Sister Mentor Coordinator</h4></td></tr>
                    <tr><td><h4>ARR</h4></td><td><h4>Annual Report Reviewer</h4></td></tr>
                    <tr><td><h4>ART</h4></td><td><h4>Annual Report Tester</h4></td></tr>
                </table>
                <br>
                <h4><strong>International Positions</h4></strong>
                <table>
                    <tr><td><h4>IT</h4></td><td><h4>IT Coordinator</h4></td></tr>
                    <tr><td><h4>EIN</h4></td><td><h4>EIN Coordinator</h4></td></tr>
                    <tr><td><h4>SMC</h4></td><td><h4>Social Media Coordinator</h4></td></tr>
                    <tr><td><h4>COR</h4></td><td><h4>Correspondence Coordinator</h4></td></tr>
                    <tr><td><h4>IIC</h4></td><td><h4>Internaitonal Inquiries Coordinator</h4></td></tr>
                    <tr><td><h4>M2M&nbsp;&nbsp;&nbsp;&nbsp;</h4></td><td><h4>M2M Committee</h4></td></tr>
                    <tr><td><h4>LIST</h4></td><td><h4>List Admin</h4></td></tr>
                </table>`,
            focusConfirm: false,
            confirmButtonText: 'Close',
            customClass: {
                popup: 'swal-wide',
                confirmButton: 'btn btn-danger'
            }
        });
    }

</script>
