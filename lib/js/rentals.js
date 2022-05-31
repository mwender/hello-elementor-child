document.querySelectorAll('.rental-button .elementor-button').forEach( item => {
  item.addEventListener('click', event => {
    event.preventDefault();

    /**
     * Get the product_id
     */
    var target = event.target;
    var parent = target.parentNode.closest('.rental-button');
    var product_id = parent.getAttribute('data-product_id');

    /**
     * Send the product_id to the server
     */
    httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function(){
      if( httpRequest.readyState === XMLHttpRequest.DONE ){
        if( httpRequest.status === 200 ){
          var responseJSON = JSON.parse( httpRequest.response );
          console.log(`🔔 AJAX request is complete. responseJSON = `, responseJSON );
          if( responseJSON.updated ){
            var activateTemplate = document.getElementById(`activate-template_${product_id}`);
            activateTemplate.innerHTML = `<div class="oaksmin-loader-wrap"><div class="oaksmin-loader"></div><p class="oaksmin-loader-note">Reloading. One moment...</p></div>`;
            setTimeout(function(){
              window.location.reload();
            },3000);
          }
        }
      }
    }
    httpRequest.open( 'POST', wpApiSettings.ep, true );
    httpRequest.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
    httpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    httpRequest.send( 'product_id=' + product_id );
  });
});