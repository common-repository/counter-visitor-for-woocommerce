var WCVisitor = {

    loading: false,
    show: function(product, position, isReload) {
        var self = this;
        if(!isReload) { var isReload = false;}
        if(self.loading) {
            console.log("Loading is true");
            return;
        }
        self.loading = true;
        jQuery.ajax({
            type: "post",
            url: WCVisitorConfig.url,
            data: "action=wcvisitor_get_counter&product="+product,
            success: function(result){

                self.loading = false;


                if(isReload) {

                    if(jQuery(".wcv-message").attr("data-counter") != result.counter) {
                        jQuery(".wcv-message").replaceWith(result.html);
                        setTimeout(function(){
                            jQuery(".wcv-message").addClass("wcv-animation-on");
                        });
                        
                        setTimeout(function(){
                            jQuery(".wcv-message").removeClass("wcv-animation-on");
                        },600);
                    }
                    return;
                }

                var data_position = JSON.parse(position);
                if(data_position[1] == 'after') {
                    jQuery(data_position[0]).after(result.html);
                }else if(data_position[1] == 'before') {
                    jQuery(data_position[0]).before(result.html);
                }else if(data_position[1] == 'inside') {
                    jQuery(data_position[0]).append(result.html);
                }
            }
        });
    },
    reload: function(product, timeToReload){
        var self = this;
        setInterval(function(){
            console.log("Conectado con LIVE MODE");
            self.show(product, false, true);           
        }, parseInt(1000 * parseInt(timeToReload)));
    }

}