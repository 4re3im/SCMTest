window.onload = function (e) {
  var GIGYA_JS_VERSION = 38.1
  var GO_CORE_JS_VERSION = 26.0
  var MARKETING_JS_VERSION = 2
  var MAIN_JS_VERSION = 3.4
  var PRODUCT_JS_VERSION = 4
  var ELEVATE_JS_VERSION = 10
  var ACTIVATE_JS_VERSION = 18.9
  var SUPPORT_JS_VERSION = 1.1

  var headElements = []
  var bodyElements = []

  var gigyaJs = document.createElement('script')
  gigyaJs.type = 'text/javascript'
  gigyaJs.src = '/packages/go_contents/js/gigya.js?v=' + GIGYA_JS_VERSION
  headElements.push(gigyaJs)

  var goCoreJs = document.createElement('script')
  goCoreJs.type = 'text/javascript'
  goCoreJs.src = '/packages/go_theme/js/go-core.js?v=' + GO_CORE_JS_VERSION
  bodyElements.push(goCoreJs)

  var bootstrapJs = document.createElement('script')
  bootstrapJs.type = 'text/javascript'
  bootstrapJs.src = '/packages/go_theme/js/bootstrap.js'
  bodyElements.push(bootstrapJs)

  var autotabJs = document.createElement('script')
  autotabJs.type = 'text/javascript'
  autotabJs.src = '/packages/go_contents/js/jquery.autotab.js'
  bodyElements.push(autotabJs)

  var activateJs = document.createElement('script')
  activateJs.type = 'text/javascript'
  activateJs.src = '/packages/go_contents/js/activate.js?v=' + ACTIVATE_JS_VERSION
  bodyElements.push(activateJs)

  var supportJs = document.createElement('script')
  supportJs.type = 'text/javascript'
  supportJs.src = '/packages/go_contents/js/support.js?v=' + SUPPORT_JS_VERSION
  bodyElements.push(supportJs)

  var marketingJs = document.createElement('script')
  marketingJs.type = 'text/javascript'
  marketingJs.src = '/packages/go_theme/js/marketing-popup.js?v=' + MARKETING_JS_VERSION
  bodyElements.push(marketingJs)

  var productJs = document.createElement('script')
  productJs.type = 'text/javascript'
  productJs.src = '/packages/go_product/js/product.js?v=' + PRODUCT_JS_VERSION
  bodyElements.push(productJs)

  var mainJs = document.createElement('script')
  mainJs.type = 'text/javascript'
  mainJs.src = '/packages/go_contents/js/main.js?v=' + MAIN_JS_VERSION
  bodyElements.push(mainJs)

  var elevateJs = document.createElement('script')
  elevateJs.type = 'text/javascript'
  elevateJs.src = '/packages/go_elevate/js/elevate.js?v=' + ELEVATE_JS_VERSION
  bodyElements.push(elevateJs)

  var rebotifyDiv = document.createElement('div');
  rebotifyDiv.id = 'rebotifyChatbox';
  var botId = document.createAttribute('botid');
  botId.value = '5a039f95653367000586111b';
  rebotifyDiv.setAttributeNode(botId);
  var rebotifyJs = document.createElement('script');
  rebotifyJs.type = 'text/javascript'
  rebotifyJs.src = 'https://enterprise.rebotify.com/js/chatbox/rebotifyChatbox.js'
  rebotifyDiv.appendChild(rebotifyJs)
  bodyElements.push(rebotifyDiv)

  headElements.forEach(function (hElement) {
    document.head.appendChild(hElement)
  })

  bodyElements.forEach(function (bElement) {
    document.body.appendChild(bElement)
  })
}