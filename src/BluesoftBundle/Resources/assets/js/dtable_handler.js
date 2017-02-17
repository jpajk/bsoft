$(document).ready(function () {
    $('#contracts-table').DataTable({
        "ajax": {
            "url": window.location.href + 'get_contract_data',
            "type": "POST"
        }
    });
});
