/**
 * Created by Di_Moon on 19.01.2015.
 */
function requestJSONbyAjax(url) {
    $.ajax({
        url: options.url,
        async: false,
        type: 'GET',
        dataType: 'json',
        data: {},
        error: function() {
            console.log('Ошибка получения данных');
            return null;
        }
    }).done(function(data) {
        return  data;
    });

    return null;
}