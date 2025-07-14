/**
 * Key Active Admin JavaScript
 * Handles key generation and UI enhancements
 */

jQuery(document).ready(function($) {
    /**
     * Generates a random license key
     * Format: XXXXX-XXXXX-XXXXX-XXXXX
     * 
     * @return {string} Random key in the specified format
     */
    function generateRandomKey() {
        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        var segments = 4;
        var segmentLength = 5;
        var key = "";
        
        for(var i = 0; i < segments; i++) {
            for(var j = 0; j < segmentLength; j++) {
                var randomIndex = Math.floor(Math.random() * chars.length);
                key += chars.charAt(randomIndex);
            }
            if(i < segments - 1) {
                key += "-";
            }
        }
        
        return key;
    }
    
    // Handle key generation in the main admin page
    $(".key-generate-btn").on("click", function(e) {
        e.preventDefault();
        var randomKey = generateRandomKey();
        $("#license_key").val(randomKey);
    });
    
    // Handle key generation in the edit page
    $(".generate-key-button").on("click", function(e) {
        e.preventDefault();
        var randomKey = generateRandomKey();
        $("#title").val(randomKey);
    });
    
    // Auto-generate a key if the field is empty on page load
    if($("#license_key").length && $("#license_key").val() === "") {
        $("#license_key").val(generateRandomKey());
    }
    
    // Style improvements for the metabox on edit page
    if($("#key_active_metabox").length) {
        $("#key_active_metabox .inside").css({
            "padding": "15px",
            "margin": "0"
        });
        
        $("#key_active_metabox label").css({
            "font-weight": "600",
            "margin-bottom": "5px",
            "display": "block"
        });
        
        $("#key_active_metabox input[type='text'], #key_active_metabox input[type='email']").css({
            "width": "100%",
            "padding": "8px 10px",
            "margin-bottom": "15px",
            "border-radius": "4px",
            "border": "1px solid #ddd"
        });
    }
}); 