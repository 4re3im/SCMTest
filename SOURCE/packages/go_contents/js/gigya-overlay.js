function showGigyaOverlay (isShown) {
  var gigyaLoader = document.querySelector('.loader') // $('.loader');
  if (isShown) {
    gigyaLoader.classList.add('loading')
  } else {
    gigyaLoader.classList.remove('loading')
  }
}