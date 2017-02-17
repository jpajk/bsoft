console.log('yadda')
$(document).ready(function () {
    $.ajax({
        type: "POST",
        url: window.location.href + 'get_contract_data',
        success: function(json) {
            console.log(JSON.parse(json));
        }
    });
});
