$(document).ready(function () {
    $("#MainContent_txtSurname").autocomplete({

        source: function (request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return;
            }
            $.ajax({
                crossDomain: true,
                type: 'POST',
                url: "http://localhost:1448/GetSurnames",

                dataType: 'json',
                data: { "Name": request.term, "CID": CID },
                processdata: true,
                success: function (result) {
                    var Surnames = JSON.parse(result.data);
                
                    cache[term] = $.map(Surnames, function (item) {
                
                        return {
                            label: item.homename,
                            value: item.homename
                        }
                    });
                    response(cache[term]);
                },
                error: function (a, b, c) {
                    debugger;
                }
            });

        },
        minLength: 2
    });
});