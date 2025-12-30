<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideenpipeline - CIP & Innovation einfach gestalten</title>
    <meta name="description" content="Beteiligen Sie Ihre Mitarbeiter aktiv am kontinuierlichen Verbesserungsprozess. Mehr Innovation, mehr Zufriedenheit, mehr Wachstum – mit ideenpipeline.de.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .innovation-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .innovation-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.1);
            border-color: rgba(79, 70, 229, 0.3);
        }

        .step-blob {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    
    <nav class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <div class="w-10 h-10 gradient-primary rounded-xl flex items-center justify-center text-white mr-3 shadow-lg shadow-indigo-200">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Ideen<span class="text-indigo-600">pipeline</span></h1>
                </div>
                
                <div class="flex items-center space-x-6">
                    @auth
                        <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant?->id ?? 'default']) }}" class="text-slate-600 hover:text-indigo-600 font-bold transition text-sm">Dashboard</a>
                    @else
                        @if($tenant)
                            <a href="{{ route('tenant.login', ['tenantId' => $tenant->id]) }}" class="bg-indigo-600 text-white px-6 py-2.5 rounded-full font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 text-sm">
                                Login
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-20 pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-6">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full mr-2 animate-pulse"></span>
                        Unser Tool für lebendigen CIP
                    </div>
                    
                    <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] mb-8 tracking-tight text-slate-900">
                        Ideen sichtbar machen – <span class="text-gradient">Innovation</span> einfach gestalten.
                    </h1>
                    
                    <p class="text-xl text-slate-600 mb-10 leading-relaxed max-w-xl">
                        Mit <strong>ideenpipeline.de</strong> beteiligen Sie Ihre Mitarbeiter aktiv am kontinuierlichen Verbesserungsprozess (CIP). Mehr Innovationen, mehr Zufriedenheit und nachhaltiges Wachstum.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="https://digitalpackt.de/register" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-slate-800 transition shadow-xl shadow-slate-200">
                            Jetzt auf Digitalpackt.de registrieren
                        </a>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl border-8 border-white">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Team Innovation" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                    <div class="absolute -top-10 -right-10 w-64 h-64 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-6 tracking-tight">Warum ideenpipeline.de?</h2>
                <p class="text-lg text-slate-600">
                    Kleine und mittelständische Unternehmen wollen ihre Mitarbeiter an der Entwicklung von Verbesserungen beteiligen. Wir machen diesen Prozess so einfach wie nie zuvor.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <div class="p-10 rounded-[2.5rem] bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-6 text-xl">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-slate-900">Die Probleme ohne System</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Verbesserungsvorschläge werden oft durch Vorgesetzte gefiltert und erreichen nicht die Entscheider. Das frustriert Mitarbeiter – Ideen bleiben ungehört.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-red-700 font-medium"><i class="fas fa-times-circle mr-3"></i> Teure Fehlentwicklungen</li>
                        <li class="flex items-center text-red-700 font-medium"><i class="fas fa-times-circle mr-3"></i> Fehlende Wertschätzung</li>
                        <li class="flex items-center text-red-700 font-medium"><i class="fas fa-times-circle mr-3"></i> Starre Strukturen</li>
                    </ul>
                </div>

                <div class="p-10 rounded-[2.5rem] bg-indigo-600 text-white shadow-2xl shadow-indigo-200">
                    <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center mb-6 text-xl">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Unsere Lösung</h3>
                    <p class="text-indigo-100 leading-relaxed mb-6">
                        Wer seine Arbeit täglich macht, weiß am besten, wie man sie effizienter gestaltet. Wir geben Ihren Mitarbeitern das Recht zur aktiven Mitgestaltung.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-white font-medium"><i class="fas fa-check-circle text-indigo-300 mr-3"></i> Transparenter Prozess</li>
                        <li class="flex items-center text-white font-medium"><i class="fas fa-check-circle text-indigo-300 mr-3"></i> Direkter Draht zu Entscheidern</li>
                        <li class="flex items-center text-white font-medium"><i class="fas fa-check-circle text-indigo-300 mr-3"></i> Unkomplizierte Beteiligung</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">So funktioniert es</h2>
                <p class="text-slate-500">In 5 einfachen Schritten zu mehr Innovation.</p>
            </div>
            
            <div class="grid md:grid-cols-5 gap-8">
                @php
                    $steps = [
                        ['icon' => 'user-plus', 'title' => 'Anmelden', 'desc' => 'Auf Digitalpackt.de registrieren'],
                        ['icon' => 'list-check', 'title' => 'Plan wählen', 'desc' => 'Passendes Paket aussuchen'],
                        ['icon' => 'globe', 'title' => 'Domain', 'desc' => 'Subdomain festlegen'],
                        ['icon' => 'users-gear', 'title' => 'Einladen', 'desc' => 'Teams & Entscheider einladen'],
                        ['icon' => 'trophy', 'title' => 'Belohnung', 'desc' => 'Optionales Belohnungssystem'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                <div class="text-center">
                    <div class="w-16 h-16 step-blob rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-black relative">
                        {{ $index + 1 }}
                        <div class="absolute -inset-2 border-2 border-indigo-100 rounded-3xl opacity-0 hover:opacity-100 transition-opacity"></div>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-2">{{ $step['title'] }}</h4>
                    <p class="text-xs text-slate-500">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-20 items-center">
                <div class="lg:w-1/2">
                    <h2 class="text-4xl font-extrabold text-slate-900 mb-8 tracking-tight">Ihr Vorteil: Transparenz & Wachstum</h2>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Mitarbeiter schlagen Verbesserungen vor und werden direkt an den Planungen beteiligt. Das integrierte <strong>Scoring</strong> zeigt transparent, wann und warum eine Idee umgesetzt wird.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <i class="fas fa-chart-line text-emerald-600 mr-4 text-xl"></i>
                            <span class="font-bold text-emerald-900">Mehr Wachstum & Sicherheit</span>
                        </div>
                        <div class="flex items-center p-4 bg-blue-50 rounded-2xl border border-blue-100">
                            <i class="fas fa-smile text-blue-600 mr-4 text-xl"></i>
                            <span class="font-bold text-blue-900">Höhere Mitarbeiterzufriedenheit</span>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 grid grid-cols-2 gap-6">
                    <div class="innovation-card p-8 bg-white rounded-[2rem]">
                        <div class="text-indigo-600 text-3xl mb-4 font-black">94%</div>
                        <p class="text-sm font-bold text-slate-900">Mitarbeiter-Beteiligung</p>
                    </div>
                    <div class="innovation-card p-8 bg-white rounded-[2rem] mt-8">
                        <div class="text-indigo-600 text-3xl mb-4 font-black">2.4x</div>
                        <p class="text-sm font-bold text-slate-900">Schnellere Umsetzung</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-900 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-8 tracking-tight">Was passiert, wenn Sie nichts tun?</h2>
            <p class="text-xl text-slate-400 mb-12">
                Stillstand hat kapitale Folgen: unzufriedene Mitarbeiter und Kunden wandern ab, Umsätze sinken, Unternehmen verlieren Wettbewerbsfähigkeit. Im schlimmsten Fall droht das Aus.
            </p>
            <div class="flex flex-wrap justify-center gap-8">
                <div class="flex items-center text-slate-400"><i class="fas fa-ban text-red-500 mr-3"></i> Stillstand</div>
                <div class="flex items-center text-slate-400"><i class="fas fa-user-minus text-red-500 mr-3"></i> Fluktuation</div>
                <div class="flex items-center text-slate-400"><i class="fas fa-arrow-down text-red-500 mr-3"></i> Umsatzverlust</div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-extrabold text-slate-900 mb-8">Starten Sie jetzt und machen Sie Innovation einfach und transparent.</h2>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                <a href="https://digitalpackt.de/register" class="bg-indigo-600 text-white px-10 py-5 rounded-2xl font-bold text-xl hover:bg-indigo-700 transition shadow-2xl shadow-indigo-200">
                    Jetzt auf Digitalpackt.de registrieren
                </a>
            </div>
            <div class="mt-10 flex justify-center space-x-8 text-sm font-bold text-slate-400">
                <a href="#" class="hover:text-indigo-600 transition">FAQ</a>
                <a href="#" class="hover:text-indigo-600 transition">Kontakt aufnehmen</a>
            </div>
        </div>
    </section>

    <footer class="bg-slate-50 border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-400 text-sm font-medium mb-6">
                &copy; {{ date('Y') }} Ideenpipeline – Ein Projekt von Digitalpackt.de
            </p>
            
            <div class="flex justify-center space-x-8 text-xs font-black uppercase tracking-widest text-slate-400">
                @php
                    $isTenant = isset($tenant) && $tenant !== null;
                    $impLink = $isTenant && Route::has('tenant.legal.impressum') ? route('tenant.legal.impressum') : '#';
                    $datLink = $isTenant && Route::has('tenant.legal.datenschutz') ? route('tenant.legal.datenschutz') : '#';
                @endphp
                <a href="{{ $impLink }}" class="hover:text-indigo-600 transition">Impressum</a>
                <a href="{{ $datLink }}" class="hover:text-indigo-600 transition">Datenschutz</a>
            </div>

            @if(isset($tenant))
                <div class="mt-8 pt-8 border-t border-slate-200">
                    <span class="bg-white border border-slate-200 px-4 py-2 rounded-full text-[10px] text-slate-400 font-bold">
                        DOMAIN: {{ $tenant->subdomain }}.ideenpipeline.de
                    </span>
                </div>
            @endif
        </div>
    </footer>

</body>
</html>