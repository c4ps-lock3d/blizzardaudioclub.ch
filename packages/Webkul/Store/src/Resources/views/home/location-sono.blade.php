<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Location Sono
    </x-slot>

    <div class="container mt-[34px] px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4 max-md:text-sm max-sm:text-xs">
        <p class="text-xl">LOCATION SONO</p>
    </div>

    <div class="container px-[60px] max-lg:px-8 max-md:px-4">
        <p class="mt-6 text-justify">
            Le label Blizzard Audio Club a acquis du matériel de sonorisation afin d’organiser ses propres événements, mais également de le mettre en location.<br><br>
            Il est adapté pour des événements intérieurs et extérieurs (sous protection des intempéries) et permet de sonoriser :<br><br>
        </p>
            <ul class="list-disc">
                <li class="ml-8">Des lives (matériel DJ non-fourni)</li>
                <li class="ml-8">Des dj-sets (matériel DJ non-fourni)</li>
                <li class="ml-8">Des concerts (instruments non-fournis)</li>
            </ul><br>
        <p class="text-justify">
            Le matériel est loué sur une base journalière. Des arrangements peuvent néanmoins être convenus, tels que des demi-journées ou des durées plus longues (Le tarif sera adapté selon). La location à l’heure n’est pas proposée.<br><br>
            Le tarif de base de la location s’élève à 200 CHF [DEUX-CENTS FRANCS] par jour. La moitié de ce montant est à verser avant la prise du matériel. L’autre moitié au retour du matériel. Les éventuels frais de réparation ou de remplacement de matériel y seront ajoutés (voir les <a class="!text-[#FADA00]" href="{{url('/images/sono/bac-soundsystem-renting-cg.pdf')}}" target="_blank">conditions générales</a>).<br><br>
        </p>
        <p>Intéressé(e) ? <a class="!text-[#FADA00]" href="{{ route('shop.home.contact_us') }}">contactez-nous via notre formulaire</a>.</p>
        <div class="mt-10 grid grid-cols-2 gap-6">
            <img class="rounded-lg border border-black" src="{{url('/images/sono/sono1.webp')}}" alt="">
            <img class="rounded-lg border border-black" src="{{url('/images/sono/sono2.webp')}}" alt="">
            <img class="rounded-lg border border-black" src="{{url('/images/sono/sono3.webp')}}" alt="">
            <img class="rounded-lg border border-black" src="{{url('/images/sono/sono4.webp')}}" alt="">
        </div>
    </div>
</x-shop::layouts>

<script>document.body.style.overflow ='scroll';</script>