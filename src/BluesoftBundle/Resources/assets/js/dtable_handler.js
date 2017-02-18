$(document).ready(function () {
    var table = $('#contracts-table');

    table.DataTable({
        "ajax": {
            "url": window.location.href + 'get_contract_data',
            "type": "POST"
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7 },
            { "data": 8 },
            { "data": 9 },
            {
                "data": 10,
                "render": function (data, type, full, meta) {
                    // todo this can be changed to use symfony js router
                    // '<a href="/contract/'+data+'/edit" class="btn btn-warning btn-top">Edytuj</a>' +
                    return '<a data-id-entity="'+data+'" href="/contract/delete/'+data+'" class="btn btn-danger">Usuń</a>';
                }
            }
        ]
    });

    // Handle row clicks

    $(table).on('click', 'tbody tr', function() {
        console.log($(this).find('a').data('id-entity'));
        window.location.href = window.location.href + 'contract/' + $(this).find('a').data('id-entity') + '/edit'

    })
});
