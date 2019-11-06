$("#city_submit").click(function (e) {
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "/app",
        data: {
            city: $('#city_city').val(),
        },
        success: function (result) {
            var html = responseHandler(result);
            $('#weather_table tr').not(function(){ return !!$(this).has('th').length; }).remove();
            $('#weather_table').append(html);
        },
        error: function (result) {
            alert('error');
        }
    });
});

function responseHandler(result) {
    let html = '';
    $.each(result, function (i, item) {
        html += '<tr><td>'
            + item.city + '</td><td>'
            + item.date + '</td><td>'
            + item.temperature + '</td><td>'
            + item.humidity + '</td><td>'
            + item.pressure + '</td></tr>';
    });

    return html;
}