document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const userId = document.getElementById('user-data').getAttribute('data-user-id');


    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        selectable: true,
        editable: true,
        events: function (fetchInfo, successCallback, failureCallback) {

            fetch('includes/fetch_all_events.php')
                .then(response => response.json())
                .then(events => successCallback(events))
                .catch(error => failureCallback(error));
        },
        
        dateClick: function (info) {
            alert('Clicked on: ' + info.dateStr);
        },

        eventClick: function (info) {
            if (confirm('Add this event to your personal calendar?')) {
                $.ajax({
                    url: 'includes/add_user_to_event.php',
                    type: 'POST',
                    data: { eventId: info.event.id },
                    success: function(response) {

                        calendar.refetchEvents();
                    },
                    error: function(error) {
                        alert("Error adding event to personal calendar.");
                        console.error(error);
                    }
                });
            }
        },


        eventMouseEnter: function(info) {
            hoverEventHtml = `
                <div id="hover-event" class="hover-end">
                    <strong>${info.event.title}</strong><br>
                    Location: ${info.event.extendedProps.location || 'Not specified'}<br>
                    Start: ${FullCalendar.formatDate(info.event.start, {
                        hour: '2-digit', minute: '2-digit', month: '2-digit', day: '2-digit', year: 'numeric'
                    })}<br>
                    End: ${FullCalendar.formatDate(info.event.end, {
                        hour: '2-digit', minute: '2-digit', month: '2-digit', day: '2-digit', year: 'numeric'
                    })}
                </div>
            `;
            $(info.el).append(hoverEventHtml);
        },
        
        eventMouseLeave: function(info) {

            $(info.el).find('#hover-event').remove();
        }
        
        

    });

    calendar.render();
});
