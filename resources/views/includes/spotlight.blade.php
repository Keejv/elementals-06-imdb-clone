<section class="section spotlight">
    @include('includes.divider')
    <div class="columns">
        @foreach($spotlights as $spotlight)
        <div class="column is-12-mobile spotlight__poster">
            <div class="spotlight__info">
                <h4>{{ $spotlight->title }}</h4>
            </div>
            <a href="/{{$spotlight->type}}s/{{ $spotlight->id or $spotlight->item_id }}">
                <div class="spotlight__image">
                    <img src="{{ $spotlight->profile_pic or $spotlight->poster }}" alt="{{ $spotlight->name or $spotlight->title }}">
                </div>
            </a>
        </div>
        @endforeach
    </div>
</section>