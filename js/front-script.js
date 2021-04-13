var beforeImageToPdf, afterImageToPdf;

/* Image of page to PDF */
function imageToPdf() {
    ( function( $ ) {

        var deferreds = [];
        var fileSettings = {
            'pageSize': pdfprnt_file_settings.page_size.toLowerCase(),
            'marginLeft': Number( pdfprnt_file_settings.margin_left ),
            'marginRight': Number( pdfprnt_file_settings.margin_right ),
            'marginBottom': Number( pdfprnt_file_settings.margin_bottom ),
            'marginTop': Number( pdfprnt_file_settings.margin_top ),
            'fileAction': pdfprnt_file_settings.file_action,
            'fileName': pdfprnt_file_settings.file_name
        };

        var pdf = new jsPDF( 'portrait', 'px', fileSettings['pageSize'] );
        var width = pdf.internal.pageSize.getWidth();
        var height = pdf.internal.pageSize.getHeight();
        // add to page Header and Footer
        function addImageCustom( canvas, imgWidth, imgHeight ) {
            pdf.addImage( canvas.toDataURL('image/jpeg', 1.0 ), 'JPEG',
                fileSettings['marginLeft'],
                fileSettings['marginTop'],
                imgWidth, imgHeight
            );
        }

        window.scrollTo(0, 0);
        if ( 'undefined' != typeof beforeImageToPdf ) {
            beforeImageToPdf();
        }
        /* hide default wp panel */
        document.getElementById("wpadminbar") ? document.getElementById("wpadminbar").style.display = "none" : '';

        var deferred = $.Deferred();
        deferreds.push( deferred.promise() );
        var canvasTop, canvasBottom;
        generateCanvas( 'body', pdf, deferred );

        function generateCanvas( selector, pdf, deferred ) {
            html2canvas( document.querySelector( 'body' ) ).then( canvas => {
                var contentWidth = canvas.width;
                var contentHeight = canvas.height;
                var headerSize = fileSettings['marginTop'] + fileSettings['marginBottom'];
                var imgHeaderSize = ( contentWidth / width * headerSize );
                // The height of the canvas which one pdf page can show;
                var pageHeight = contentWidth / width * height;

                // the height of canvas that haven't render to pdf
                var leftHeight = contentHeight;
                var imgWidth = width - fileSettings['marginLeft'] - fileSettings['marginRight'];
                var imgHeight =  width / contentWidth * contentHeight - headerSize;

                if ( ( leftHeight + headerSize ) < pageHeight ) {
                    addImageCustom( canvas, imgWidth, imgHeight + headerSize );
                } else {
                    var countPages = Math.ceil(leftHeight / pageHeight );
                    var leftHeightAfter = ( countPages * headerSize ) + leftHeight;

                    var canvasNew = document.createElement('canvas');
                    canvasNew.width = contentWidth;
                    //var theColorBody = $('body').css("background-color");
                    var row = 0;
                    for ( var i = 0; i < countPages - 1; i++ ) {
                        canvasNew.height = pageHeight - imgHeaderSize;
                        var context = canvasNew.getContext('2d');
                        context.drawImage( canvas, 0, i * -pageHeight + row );
                        imgHeight = ( width / contentWidth * pageHeight - headerSize );
                        let n = 0;
                        // var imgData = context.getImageData( fileSettings['marginLeft'], 0, contentWidth - fileSettings['marginRight'] - fileSettings['marginLeft'], pageHeight );
                        // let n = 1;
                        // let j = imgData.data.length - ( contentWidth - fileSettings['marginRight'] - fileSettings['marginLeft'] ) * 4 * n;
                        // let rgb;
                        // let lastPx = imgData.data.length;
                        // while ( j != lastPx ) {
                        //     rgb = 'rgb(' + imgData.data[j] + ', ' + imgData.data[j + 1] + ', ' + imgData.data[j + 2] + ')';
                        //     j += 4;
                        //     if ( rgb != theColorBody ) {
                        //         n++;
                        //         j = imgData.data.length - ( contentWidth - fileSettings['marginRight'] - fileSettings['marginLeft'] ) * 4 * n;
                        //         lastPx = imgData.data.length - ( contentWidth - fileSettings['marginRight'] - fileSettings['marginLeft'] ) * 4 * ( n - 1 );
                        //     } else if ( n == 50 ) {
                        //         break;
                        //     }
                        // }
                        canvasNew.height = pageHeight - n;
                        context = canvasNew.getContext('2d');
                        context.drawImage( canvas, 0, i * -pageHeight + row );
                        addImageCustom( canvasNew, imgWidth, imgHeight );
                        leftHeight -= pageHeight;
                        pdf.addPage();
                        row = row + n;
                    }

                    leftHeightAfter += row;
                    var countPagesAfter = Math.ceil( leftHeightAfter / pageHeight );
                    if ( countPagesAfter != countPages ) {

                        canvasNew.height = pageHeight - imgHeaderSize;
                        context = canvasNew.getContext('2d');
                        context.drawImage( canvas, 0, i * -pageHeight + row );
                        addImageCustom( canvasNew, imgWidth, imgHeight );

                        pdf.addPage();
                        i++;
                        leftHeight -= pageHeight;
                        row += imgHeaderSize;
                    }

                    canvasNew.height = leftHeight + row;
                    context.drawImage( canvas, 0, i * -pageHeight + row );
                    imgHeightLast = width / contentWidth * canvasNew.height;
                    addImageCustom( canvasNew, imgWidth, imgHeightLast );

                }
                deferred.resolve();
            });
        }

        if ( 'undefined' != typeof afterImageToPdf ) {
            afterImageToPdf();
        }
        /* show default wp panel */
        document.getElementById("wpadminbar") ? document.getElementById("wpadminbar").style.display = "block" : '';

        // executes after adding all images
        $.when.apply( $, deferreds ).then( function() {
            if ( 'open' == fileSettings['fileAction'] ) {
                pdf.setProperties({
                    title: fileSettings['fileName']
                });
                //pdf.autoPrint();
                window.open( pdf.output('bloburl') );
                /*  IFRAME
                var string = pdf.output('datauristring');
                var iframe = "<head><style>*{margin: 0; padding: 0;}</style><title>" + fileSettings['fileName'] + "</title></head>" +
                    "<body><iframe width='100%' height='1000px' src='" + string + "'></iframe></body>"

                var x = window.open();
                x.document.open();
                x.document.write( iframe );
                x.document.close();*/

                //pdf.output( 'dataurlnewwindow' );
            } else {
                pdf.save( fileSettings['fileName'] );
            }
        });

    } )( jQuery );
}