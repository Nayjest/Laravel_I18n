<ul class="nav navbar-nav navbar-right">
    @if(empty($compact))
    <li class="navbar-text visible-lg">Language {{App::getLocale()}}</li>
    @endif
    <li>
        <div class="btn-group btn-navbar @if(empty($compact)) hidden-lg @endif" style="padding: 8px 20px 0 0">
            @foreach(Nayjest\I18n\Facades\I18n::getSupportedLanguages() as $id => $label)
                <a
                    href="{{route('i18n.switchLanguage', [$id])}}"
                    class="btn btn-sm btn-info @if(App::getLocale()===$id)active @endif"
                >
                    {{ ucfirst($id) }}
                </a>
            @endforeach
        </div>
        @if(empty($compact))
        <div class="btn-group btn-navbar visible-lg" style="padding: 8px 20px 0 0">
            @foreach(Nayjest\I18n\Facades\I18n::getSupportedLanguages() as $id => $label)
            <a
                href="{{route('i18n.switchLanguage', [$id])}}"
                class="btn btn-sm btn-info @if(App::getLocale()===$id)active @endif"
            >
                {{$label}}
            </a>
            @endforeach
        </div>
        @endif
    </li>
    <li></li>
</ul>