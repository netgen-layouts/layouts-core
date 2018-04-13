class GoogleMap {
    constructor(el) {
        this.el = el;
        this.latitude = parseFloat(this.el.dataset.latitude);
        this.longitude = parseFloat(this.el.dataset.longitude);
        this.zoom = parseInt(this.el.dataset.zoom);
        this.mapType = this.el.dataset.mapType;
        this.showMarker = this.el.hasAttribute('data-show-marker');

        this.init();
    }

    init() {
        this.map = new google.maps.Map(this.el, {
          center: {lat: this.latitude, lng: this.longitude},
          zoom: this.zoom,
          mapTypeId: google.maps.MapTypeId[this.mapType],
          scrollwheel: false
        });

        if (this.showMarker){
          this.marker = new google.maps.Marker({
            position: {lat: this.latitude, lng: this.longitude},
            map: this.map,
            title: ''
          });
        }
    }
}

window.addEventListener('load', function() {
    if (typeof google === 'undefined' || typeof google.maps === 'undefined'){
        return;
    }

    const googleMaps = document.getElementsByClassName('ngbm-map-embed');
    [].forEach.call(googleMaps, (el) => {
        const googleMap = new GoogleMap(el);
    });
});
