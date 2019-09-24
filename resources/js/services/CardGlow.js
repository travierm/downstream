import ColorThief from 'colorthief';

export default class CardGlow {

    getPalette(thumbnailURL, callback) {
        const colorThief = new ColorThief();
        var img = new Image();
        img.onload = function () {
            var colorThief = new ColorThief();
            colorThief.getColor(img);
        };
        img.crossOrigin = 'Anonymous';
        img.src = 'http://cors-anywhere.herokuapp.com/' + thumbnailURL;

        if(img.complete) {
            callback(colorThief.getColor(img));
        } else {
            img.addEventListener('load', function () {
                callback(colorThief.getPalette(img));
            });
        }
    }

    getColorString(palette)
    {
        var colors = [];
        for (var i = 0; i <= palette.length; i++) {
            var color = palette[i];
            if (Array.isArray(color)) {
                var colorString = "rgb(" + color.join(', ') + ")";
                colors.push(colorString);
            }
        }
        var colorString = colors.join(', ');

        return colorString
    }

    enableElementGlow(elementQuery, palette)
    {       
        var colorString = this.getColorString(palette);

        $(elementQuery).attr('style', `background: linear-gradient(305deg, ${colorString});
        background-size: 1000% 1000%;
        webkit-animation: AnimationName 28 s ease infinite;
        moz-animation: AnimationName 28 s ease infinite;
        animation: AnimationName 28 s ease infinite;
        @-webkit-keyframes AnimationName {
            0 % {
                background - position: 0 % 10 %
            }

            50 % {
                background - position: 100 % 91 %
            }

            100 % {
                background - position: 0 % 10 %
            }
        }

        @-moz-keyframes AnimationName {
            0 % {
                background - position: 0 % 10 %
            }

            50 % {
                background - position: 100 % 91 %
            }

            100 % {
                background - position: 0 % 10 %
            }
        }

        @keyframes AnimationName {
            0 % {
                background - position: 0 % 10 %
            }

            50 % {
                background - position: 100 % 91 %
            }

            100 % {
                background - position: 0 % 10 %
            }
        }`)
    }

    disableElementGlow(elementQuery)
    {
        $(elementQuery).attr('style', '');
    }
}