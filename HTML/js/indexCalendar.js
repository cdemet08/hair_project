YUI().use('calendar', 'datatype-date', 'datatype-date-math', function(Y) {


    // Switch the calendar main template to the included two pane template


    // Create a new instance of calendar, setting the showing of previous
    // and next month's dates to true, and the selection mode to multiple
    // selected dates. Additionally, set the disabledDatesRule to a name of
    // the rule which, when matched, will force the date to be excluded
    // from being selected. Also configure the initial date on the calendar
    // to be July of 2011.


    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth(); //January is 0!
    var yyyy = today.getFullYear();


    var calendar = new Y.Calendar({
        contentBox: "#mycalendar",
        width:"305px",
        showPrevMonth: true,
        showNextMonth: true,
        selectionMode: 'multiple',
        disabledDatesRule: "disables",

        date: new Date(yyyy,mm,dd)

        //date: new Date(2016,10,25)

    }).render();

// Create a set of rules to match specific dates. In this case,
// the "sundays" rule will match any sunday,

    var rules = {

        "all": {
            "all": {
                "all":{
                    "0":"disables"
                }
            },
            "9": {
                "28": "disables",
                "1":"disables"
            },
            "2":{
                "25":"disables"
            },
            "11":{
                "25":"disables"
            },
            "0":{
                "1":"disables"
            }
        }



    };




// Set the calendar customRenderer, provides the rules defined above,
// as well as a filter function. The filter function receives a reference
// to the node corresponding to the DOM element of the date that matched
// one or more rule, along with the list of rules. Check if one of the
// rules is "all_weekends", and if so, apply a custom CSS class to the
// node.
    calendar.set("customRenderer", {
        rules: rules,
        filterFunction: function (date, node, rules) {
            if (Y.Array.indexOf(rules, 'argia') >= 0) {
                node.addClass("redtext");
            }
        }
    });
    var dtdate = Y.DataType.Date;


// When selection changes, output the fired event to the
// console. the newSelection attribute in the event facade
// will contain the list of currently selected dates (or be
// empty if all dates have been deselected).
    calendar.on("selectionChange", function (ev) {

        // Get the date from the list of selected
        // dates returned with the event (since only
        // single selection is enabled by default,
        // we expect there to be only one date)
        var newDate = ev.newSelection[0];
   var user = "/index.php?appointment=";
        var newSite = user.concat(dtdate.format(newDate));

        window.location.href = newSite;

        // Format the date and output it to a DOM
        // element.
        Y.one("#selecteddate").setContent(dtdate.format(newDate));
    });



});
