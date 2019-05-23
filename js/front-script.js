
/* Image of page to PDF */
function imageToPdf() {
    window.scrollTo(0, 0);
    html2canvas( document.querySelector('body') ).then( canvas => {
       var fileSettings = {
            'pageSize'     : pdfprnt_file_settings.page_size.toLowerCase(),
            'marginLeft'   : Number( pdfprnt_file_settings.margin_left ),
            'marginRight'  : Number( pdfprnt_file_settings.margin_right ),
            'marginBottom' : Number( pdfprnt_file_settings.margin_bottom ),
            'marginTop'    : Number( pdfprnt_file_settings.margin_top ),
            'fileAction'   : pdfprnt_file_settings.file_action,
            'fileName'     : pdfprnt_file_settings.file_name
        };
        var pdf = new jsPDF( 'portrait', 'pt', fileSettings['pageSize'] );
        var width = pdf.internal.pageSize.getWidth();
        var height = pdf.internal.pageSize.getHeight();
        var contentWidth = canvas.width;
        var contentHeight = canvas.height;

        //The height of the canvas which one pdf page can show;
        var pageHeight = contentWidth / width * height;

        //the height of canvas that haven't render to pdf
        var leftHeight = contentHeight;
        var imgWidth = width - fileSettings['marginLeft'] - fileSettings['marginRight'];
        var imgHeight = width / contentWidth * contentHeight - fileSettings['marginTop'] - fileSettings['marginBottom'];
        var pageData = canvas.toDataURL( 'image/jpeg', 1.0 );

        if (leftHeight < pageHeight) {
            pdf.addImage(pageData, 'JPEG', fileSettings['marginLeft'], fileSettings['marginTop'], imgWidth, imgHeight );
        } else {
            var countPages = Math.ceil( leftHeight / pageHeight )
            var canvasNew = document.createElement( 'canvas' );
            canvasNew.width = contentWidth;
            canvasNew.height = pageHeight;
            var context = canvasNew.getContext( '2d' );
            for ( var i = 0; i < countPages-1 ; i++) {
                context.drawImage( canvas, 0, i * -pageHeight );
                imgHeight = width / contentWidth * canvasNew.height - fileSettings['marginTop']  - fileSettings['marginBottom'];
                pdf.addImage( canvasNew.toDataURL( 'image/jpeg', 1.0 ), 'JPEG', fileSettings['marginLeft'], fileSettings['marginTop'], imgWidth, imgHeight );
                leftHeight -= pageHeight;
                pdf.addPage();
            }
            canvasNew.height = leftHeight;
            context.drawImage( canvas, 0,  (countPages-1) * -pageHeight );
            imgHeight =  width / contentWidth * canvasNew.height;
            pdf.addImage( canvasNew.toDataURL( 'image/jpeg', 1.0 ), 'JPEG', fileSettings['marginLeft'], fileSettings['marginTop'], imgWidth, imgHeight );
        }

        if ( 'open' == fileSettings['fileAction'] ) {
            pdf.setProperties({
                title: fileSettings['fileName']
            });
            window.open( pdf.output( 'bloburl' ) );
        } else {
            pdf.save( fileSettings['fileName'] );
        }
    });
}