document.querySelector('#signup-form').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  let formObject = {};
  formData.forEach(function(value, key) {
    formObject[key] = value;
  });

  formObject['admin'] = false;

  const json = JSON.stringify(formObject);

  

  fetch('./users', {
    method: 'POST',
    body: json,
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    console.log('Success:', data);
  })
  .catch(error => {
    console.error('Error:', error);
  });
});