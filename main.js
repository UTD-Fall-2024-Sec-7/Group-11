document.getElementById('eventForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const title = event.target.title.value;
    const datetime = event.target.datetime.value;
    const info = event.target.info.value;
    const location = event.target.location.value;

    const events = JSON.parse(localStorage.getItem('events')) || [];
    events.push({ title, datetime, info, location });
    localStorage.setItem('events', JSON.stringify(events));

    alert("Event saved successfully!");
    window.location.href = 'events.html';
});
