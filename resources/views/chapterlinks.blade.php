<!DOCTYPE html>
<html lang="en">
<head>
<title>MOMS Club: Chapter Links</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('css2/style.css') }}" type="text/css" media="screen">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<section id="content">
    <div class="container_24">
        <div class="wrapper p10">

            <div class="border-horiz2"></div>
                <h4>International Chapters</h4>
                <div class="border-horiz2"></div>
                <div class="masonry">
                    @php
                        $previousCountry = null; // Initialize previous state variable
                    @endphp

                    @foreach($international as $chapter)
                        @if($chapter->country !== $previousCountry)
                            <!-- Display state name only if it's different from the previous one -->
                            <div id="{{ $chapter->country }}" class="state masonry-item col-md-4" >
                                <h5 style="margin-bottom: 0px">{{ $chapter->country }}</h5>
                        @endif

                        <div class="chapter">
                            @if($chapter->website_status == 1)
                                <a href="{{ $chapter->website_url }}" target="_blank">{{ $chapter->name }}</a>
                            @else
                                <a href="https://momsclub.org/chapters/find-a-chapter/" target="_blank">{{ $chapter->name }}</a>
                            @endif
                        </div>

                        @php
                            $previousState = $chapter->country; // Update previous state
                        @endphp

                        @if(!$loop->last && $chapter->country !== $chapters[$loop->index + 1]->country)
                            <!-- Close state div if next chapter belongs to a different state -->
                            </div>
                        @endif

                        @if($loop->last)
                            <!-- Close state div if it's the last chapter -->
                            </div>
                        @endif
                    @endforeach
                </div>

                <br>
                <br>

            <div class="border-horiz2"></div>
          <h4>USA Chapters</h4>
          <div class="border-horiz2"></div>
                    <a href="#AL">Alabama</a> | <a href="#AK" >Alaska</a> | <a href="#AZ" >Arizona</a> | <a href="#AR" >Arkansas</a> | <a href="#CA" >California</a> | <a href="#CO" >Colorado</a> | <a href="#CT" >Connecticut</a> | <a href="#DE" >Delaware</a> | <a href="#DC" >District of Columbia </a> | <a href="#FL">Florida</a> | <a href="#GA" >Georgia</a> | <a href="#HI" >Hawaii</a> | <a href="#ID" >Idaho</a> |
                    <a href="#IL" >Illinois</a> | <a href="#IN" >Indiana</a> | <a href="#IA" >Iowa</a> | <a href="#KS">Kansas</a> | <a href="#KY">Kentucky</a> | <a href="#LA">Louisiana</a> | <a href="#ME" >Maine</a> | <a href="#MD">Maryland</a> | <a href="#MA" >Massachusetts</a> | <a href="#MI">Michigan</a> | <a href="#MN" >Minnesota</a> | <a href="#MS" >Mississippi</a> | <a href="#MO">Missouri</a> |
                    <a href="#MT" >Montana</a> | <a href="#NE" >Nebraska</a> | <a href="#NV" >Nevada</a> | <a href="#NH" >New Hampshire</a> | <a href="#NJ" >New Jersey</a> | <a href="#NM" >New Mexico</a> | <a href="#NY" >New York</a> | <a href="#NC" >North Carolina</a> | <a href="#ND" >North Dakota</a> | <a href="#OH" >Ohio</a> | <a href="#OK" >Oklahoma</a> | <a href="#OR" >Oregon</a> | <a href="#PA" >Pennsylvania</a> |
                    <a href="#RI" >Rhode Island</a> | <a href="#SC" >South Carolina</a> | <a href="#SD" >South Dakota</a> | <a href="#TN" >Tennessee</a> | <a href="#TX" >Texas</a> | <a href="#UT" >Utah</a> | <a href="#VT" >Vermont</a> | <a href="#VA" >Virginia</a> | <a href="#WA" >Washington</a> | <a href="#WV" >West Virginia</a> | <a href="#WI" >Wisconsin</a> | <a href="#WY" >Wyoming</a>
            <hr>
            <div class="masonry2">
                @php
                    $previousState = null; // Initialize previous state variable
                @endphp

                @foreach($chapters as $chapter)
                    @if($chapter->state_long_name !== $previousState)
                        <!-- Display state name only if it's different from the previous one -->
                        <div id="{{ $chapter->state_short_name }}" class="state masonry-item col-md-4" style="margin-top: 30px">
                            <h5 style="margin-bottom: 0px">{{ $chapter->state_long_name }}</h5>
                    @endif

                    <div class="chapter">
                        @if($chapter->website_status == 1)
                            <a href="{{ $chapter->website_url }}" target="_blank">{{ $chapter->name }}</a>
                        @else
                            <a href="https://momsclub.org/chapters/find-a-chapter/" target="_blank">{{ $chapter->name }}</a>
                        @endif
                    </div>

                    @php
                        $previousState = $chapter->state_long_name; // Update previous state
                    @endphp

                    @if(!$loop->last && $chapter->state_long_name !== $chapters[$loop->index + 1]->state_long_name)
                        <!-- Close state div if next chapter belongs to a different state -->
                        </div>
                    @endif

                    @if($loop->last)
                        <!-- Close state div if it's the last chapter -->
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>
<script>
$(document).ready(function() {
    var elem = document.querySelector('.masonry');
    var msnry = new Masonry(elem, {
        itemSelector: '.masonry-item',
        columnWidth: '.masonry-item',
        percentPosition: true
    });
});

$(document).ready(function() {
    var elem = document.querySelector('.masonry2');
    var msnry = new Masonry(elem, {
        itemSelector: '.masonry-item',
        columnWidth: '.masonry-item',
        percentPosition: true
    });
});

</script>
</html>
