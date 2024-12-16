<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Label
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <p class="text-xl">LABEL</p>
    </div>

    <div class="container px-[60px] max-lg:px-8 max-md:px-4">
        <p class="text-justify mt-6 mb-6">
        Blizzard Audio Club est un label indépendant de musique à tendances électroniques créé autour de l'ambition de sortir des disques, cassettes et autres supports sonores en tout genre. Le label loclois a aussi pour vocation d'être une aide pour les artistes dans la production de leurs œuvres ainsi qu'une plateforme de promotion et de visibilité pour ces dernières, au travers d’un travail de communication pointu et de l’organisation d’événements. Riche de bientôt 30 sorties depuis sa création, le label compte dans ses rangs les artistes suivants :
        @foreach($artistes as $artiste)
            @if($loop->last)
                et <a class="!text-[#FADA00]" href="/artistes/{{ $artiste->slug}}-{{ $artiste->id}}">{{ $artiste->name}}</a>.
            @else
                <a class="!text-[#FADA00]" href="/artistes/{{ $artiste->slug}}-{{ $artiste->id}}">{{ $artiste->name}}</a>,
            @endif
        @endforeach
        <br><br>Il est ici important de relever le fait que le label a toujours mis un point d’honneur à défendre la scène musicale suisse et régional, comme le prouve sa liste d'artistes, tout en leur permettant un rayonnement dans leur pays mais aussi à l'international. En effet, le permet aux groupes qu'il soutient de se faire un nom sur la scène suprarégionale mais aussi de toucher un plus large public, hors des frontières, grâce large au tissu professionnel de partenaires qu'il a développé.<br><br>
        De plus, au lieu de se figer dans le style électronique qui caractérisait les premières sorties, Blizzard Audio Club a pris le choix de s’ouvrir afin de faire profiter à une plus large palette d’artistes de son expertise et support. Fondé en 2018 sous la forme associative, BAC (pour Blizzard Audio Club) jouit déjà d’une certaine renommée dans le paysage culturel. Ses sorties sont fréquemment diffusées dans de multiples médias.
        </p>
        
        <h1 class="mb-6 text-xl">LE COMITÉ</h1>
        <p class="text-justify mb-6">Nous sommes issus de milieux très différents, pas forcément lié au monde de la musique. C'est ce qui fait la richesse de notre label : chacun d'entre nous apporte son expérience et des points de vue différents qui permettent de trouver des solutions innovantes.</p>
        <div class="grid grid-cols-5 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/etienne.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Etienne</b>, artiste connu sous le nom de Pavel, donne la direction artistique et musicale et fait profiter au label de ses expériences dans cette industrie.
                </p>
            </div>
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/nestor.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Nestor</b> est tombé dans la marmite hôtelière quand il était petit, ce qui lui permet de maîtriser les aspects administratifs et financiers. 
                </p>
            </div>
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/nicolas.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Nicolas</b>, techno-bricoleur dès son plus jeune âge, a toujours été passionné par l'informatique et est en charge des parties techniques du projet. 
                </p>
            </div>
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/matheo.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Mathéo</b>, passionné de musique et ayant une forte envie de la partager assure la promotion des sorties.
                </p>
            </div>
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/nathan.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Nathan</b>, la folie pétillante alliée à la précision de son art en font le profil parfait pour s’occuper de l’identité visuelle graphique du label.
                </p>
            </div>
        </div>
        <h1 class="mb-6 mt-6 text-xl">ILS ONT PARTICIPÉS À NOTRE HISTOIRE</h1>
        <div class="grid grid-cols-5 gap-6 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/michael.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Michael</b>, photographe de passion, mélange finesse, originalité, décalage et audace avec la petite touche de sarcasme et humour qui rendent son travail toujours plus surprenant.
                </p>
            </div>
            <div id="backgroundCard" class="p-4 rounded-lg border border-black">
                <img src="{{url('/images/label-members/mona.webp')}}" alt="..." style="border-radius:50% 50%;border:0.15em solid black">
                <p class="mt-2">
                    <b>Mona</b>, sérigraphe de formation, est une hyperactive de la vie sociale et culturelle. C’est elle qui était derrière notre toute première série de t-shirts «BAC Classic». Amoureuse de bass-music, vous ne regretterez pas une soirée passée en club avec elle.
                </p>
            </div>
            <div id="backgroundCard" class="p-8 rounded-lg border border-black">
                <img src="{{url('/images/sponsors/sponsor-bcne.png')}}" alt="..." style=""><br><br>
                <img src="{{url('/images/sponsors/sponsor-canton-ne.png')}}" alt="..." style=""><br><br>
                <img src="{{url('/images/sponsors/sponsor-loro.png')}}" alt="..." style="">
            </div>
        </div>
    </div>
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>