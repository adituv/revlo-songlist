var updatePageAjax = function(page) {
    page = Number(page);
    $.ajax({
        url: "songlist_internal.php",
        data: {
            page: page
        },
        type: "GET",
        dataType: "json"
    }).done(function(json) {
        var reqtable = $( "table#songrequests > tbody" );
        reqtable.empty();

        for (req in json.requests) {
            var date = moment(json.requests[req].created_at);
            date.zone(5);
            var dateString = date.format("HH:mm:ss, D MMM YYYY");
            $('<tr>').append($('<th>', { scope: 'row' }).append(Number(req) + 1))
                     .append($('<td>').append( json.requests[req].sub === 1 ? "Y" : "N" ))
                     .append($('<td>').append( json.requests[req].user_input.song ))
                     .append($('<td>').append( json.requests[req].username ) )
                     .append($('<td>').append( dateString ))
                     .appendTo(reqtable);
        }
        var filter_func = function (ix) {
            return ix === page;
        };
        $( "ul.pagination > li" ).removeClass("active");
        $( "ul.pagination > li" ).filter(function (ix) {
            return ix === page;
        }).addClass("active");

        var prev = $( "ul.pagination > li:first-child > a" );
        prev.attr("data-page", page-1);
        if(page === 1) {
            prev.attr("href", "?page=1" );
            prev.parent().addClass("disabled");
        }
        else {
            prev.attr("href", "?page=" + (page-1) );
            prev.parent().removeClass("disabled");
        }

        var next = $( "ul.pagination > li:last-child > a" );
        next.attr("data-page", page+1);
        if(page == json.numpages) {
            next.attr("href", "?page=" + json.numpages);
            next.parent().addClass("disabled");
        }
        else {
            next.attr("href", "?page=" + (page+1));
            next.parent().removeClass("disabled");
        }
    });
}

$("ul.pagination > li > a").click(function() {
    var elem = $( this );

    if (!elem.parent().hasClass("disabled")) {
        updatePageAjax(elem.attr("data-page"));
    }
    event.preventDefault();
    return false;
});
