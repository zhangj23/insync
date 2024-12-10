document.addEventListener('DOMContentLoaded', function () {

    fetch('includes/fetch_user_events.php')
    .then(response => response.json())
    .then(events => {
       eventsList = document.getElementById('user-events-list');
       eventsList.innerHTML = '';

       events.sort((a, b) => new Date(a.start) - new Date(b.start));
       
       events.forEach(event => {
        start = new Date(event.start);
        end = new Date(event.end);
        
        var now = new Date();

        if (end < now) {
            return;
          }
          
          formattedStart = FullCalendar.formatDate(start, {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
              hour12: true
          });
          formattedEnd = FullCalendar.formatDate(end, {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
              hour12: true
          });

          listItem = document.createElement('li');
          listItem.innerHTML = `<strong>${event.title}</strong> - ${formattedStart} to ${formattedEnd}`;
          eventsList.appendChild(listItem);
       });
    })
    .catch(error => console.error('Error fetching user events:', error));



    const calendarEl = document.getElementById('calendar');

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

            fetch('includes/fetch_user_events.php')
                .then(response => response.json())
                .then(events => successCallback(events))
                .catch(error => failureCallback(error));
        },
        dateClick: function (info) {
            alert('Clicked on: ' + info.dateStr);
        },
        eventClick: function (info) {
            if (confirm('Do you want to delete this event from your personal calendar?')) {
                $.ajax({
                    url: 'includes/delete_event.php',
                    type: 'POST',
                    data: { eventId: info.event.id },
                    success: function(response) {
                        alert("Event successfully removed!");
                        calendar.refetchEvents();
                    },
                    error: function(error) {
                        alert("Error removing event from personal calendar.");
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
