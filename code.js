function onstart() {
   document.getElementById('defaultOpen').click();
  // alert(document.getElementById('defaultOpen').name);
}


function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName('tabcontent');
  for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = 'none';
  }
  tablinks = document.getElementsByClassName('tablinks');
  for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(' active', '');
  }
  document.getElementById(cityName).style.display = 'block';
  evt.currentTarget.className += ' active';
}
function additem(form){
  // document.getElementById("add").submit();
  // document.forms["add"].submit();
  // form.submit();
  this.parentNode.submit()
}
