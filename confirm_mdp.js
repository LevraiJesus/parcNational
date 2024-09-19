var popoverTrigger = new bootstrap.Popover(document.querySelector('#confirm-password'), {
    trigger: 'manual'
});

document.querySelector('#confirm-password').addEventListener('blur', function() {
    if (this.value == document.querySelector('#password').value) {
        popoverTrigger.hide();
    } else {
        popoverTrigger.show();
    }
})


document.querySelector('#password').addEventListener('input', function() {
  if(this.value.length >= 8) {
    changeTextColor(document.querySelector('#eight-characters'),true);
  } else {
    changeTextColor(document.querySelector('#eight-characters'),false);
  }

  if(this.value.match(/[A-Z]/)) {
    changeTextColor(document.querySelector('#uppercase-letter'),true);
  } else {
    changeTextColor(document.querySelector('#uppercase-letter'),false);
  }

  if(this.value.match(/[a-z]/)) {
    changeTextColor(document.querySelector('#lowercase-letter'),true);
  } else {
    changeTextColor(document.querySelector('#lowercase-letter'),false);
  }

  if(this.value.match(/[0-9]/)) {
    changeTextColor(document.querySelector('#number'),true);
  } else {
    changeTextColor(document.querySelector('#number'),false);
  }

  if(this.value.match(/[^A-Za-z0-9]/)) {
    changeTextColor(document.querySelector('#special-character'),true);
  } else {
    changeTextColor(document.querySelector('#special-character'),false);
  }

  
});

function changeTextColor(element,valid){
  if(valid){
    element.classList.remove('text-danger');
    element.classList.add('text-success');
  } else {
    element.classList.add('text-danger');
    element.classList.remove('text-success');
  }
}