<span @if(empty($compact)) class="hidden-lg" @endif>
    @foreach(Nayjest\I18n\Facades\I18n::getSupportedLanguages() as $id => $label)
        <a href="{{route('i18n.switchLanguage', [$id])}}"

           @if(App::getLocale()===$id) class="text-muted" @else class="text-primary" @endif
        >
            {{ucfirst($id)}}
        </a>
    @endforeach
</span>

@if(empty($compact))
    <span class="visible-lg">
        Language:
        @foreach(Nayjest\I18n\Facades\I18n::getSupportedLanguages() as $id => $label)
            <a
                href="{{route('i18n.switchLanguage', [$id])}}"
                @if(App::getLocale()===$id) class="text-muted" @else class="text-primary" @endif
            >
                {{$label}}
            </a>
        @endforeach
    </span>
@endif