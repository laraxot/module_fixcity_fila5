<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

/**
 * Domande che un cittadino fa davvero su FixCity.
 *
 * Non è lorem e non è il comune generico del riferimento Design Comuni.
 * Ogni voce chiude un dubbio sul percorso segnalazione → pratica → esito.
 *
 * @phpstan-type FaqEntry array{slug: non-empty-string, sort_order: int, question: non-empty-string, answer: non-empty-string}
 */
final class CivicFaqCatalog
{
    /**
     * @return list<FaqEntry>
     */
    public static function forLocale(string $locale): array
    {
        $all = self::all();

        return $all[$locale] ?? $all['it'];
    }

    /**
     * @return list<array{question: non-empty-string, answer: non-empty-string}>
     */
    public static function pairs(string $locale): array
    {
        $pairs = [];
        foreach (self::forLocale($locale) as $entry) {
            $pairs[] = [
                'question' => $entry['question'],
                'answer' => $entry['answer'],
            ];
        }

        return $pairs;
    }

    /**
     * @return array<string, list<FaqEntry>>
     */
    public static function all(): array
    {
        return [
            'it' => self::italian(),
            'en' => self::english(),
            'de' => self::german(),
            'es' => self::spanish(),
        ];
    }

    /**
     * @return list<FaqEntry>
     */
    private static function italian(): array
    {
        return [
            self::entry('come-invio-una-segnalazione', 10, 'Come invio una segnalazione?', 'Apri «Segnala», descrivi cosa non funziona, scegli la categoria (strada, illuminazione, rifiuti, verde) e indica il luogo sulla mappa. Una foto del punto esatto aiuta l’ufficio a capire senza un sopralluogo in più.'),
            self::entry('devo-registrarmi', 20, 'Devo registrarmi per segnalare?', 'Per ricevere gli aggiornamenti della pratica serve un account: nome, email, password e il consenso all’informativa privacy. Senza account puoi comunque consultare l’elenco pubblico delle segnalazioni.'),
            self::entry('nome-visibile', 30, 'Il mio nome compare sulla segnalazione?', 'L’ufficio vede l’account con cui hai inviato la pratica. Nell’elenco pubblico resta il problema, il luogo e lo stato: non pubblichiamo il tuo nome.'),
            self::entry('allego-una-foto', 40, 'Come allego una foto?', 'Nel modulo c’è il campo immagini. Una foto di giorno, con un riferimento riconoscibile (civico, fermata, incrocio), vale più di una descrizione lunga. Non inquadrare targhe, volti o documenti di altre persone.'),
            self::entry('indico-il-luogo', 50, 'Come indico il luogo del problema?', 'Cerca l’indirizzo oppure sposta il punto sulla mappa. Se è un incrocio, un parco o un marciapiede, aggiungi nel testo il lato della strada o un riferimento vicino.'),
            self::entry('seguo-lo-stato', 60, 'Come seguo lo stato della segnalazione?', 'Con il codice che ricevi usi «Traccia», anche senza entrare. Dall’area personale vedi tutte le pratiche del tuo account. Lo stato passa da ricevuta a in carico e poi a conclusa.'),
            self::entry('tempi-di-risposta', 70, 'Quanto tempo serve per una prima risposta?', 'Dipende dall’ufficio e dall’urgenza. Una buca in carreggiata e un cestino pieno non hanno la stessa priorità. La pratica resta consultabile e, quando c’è un esito, trovi una nota nello stato.'),
            self::entry('chi-legge', 80, 'Chi legge la segnalazione?', 'La prende l’ufficio competente per la categoria: strade, illuminazione, rifiuti o verde. FixCity non sostituisce l’ufficio: tiene il filo tra la tua richiesta e chi deve intervenire.'),
            self::entry('aggiorno-una-segnalazione', 90, 'Posso aggiornare una segnalazione già inviata?', 'Non aprirne una seconda sullo stesso punto. Se hai una foto nuova, un orario o un indirizzo più preciso, aggiungilo alla pratica già aperta, così resta nello stesso fascicolo.'),
            self::entry('pericolo-immediato', 100, 'Come segnalo un pericolo immediato?', 'Per un rischio in corso — fuga di gas, albero sulla carreggiata, persona in pericolo — chiama i numeri di emergenza. La segnalazione sul sito serve a far seguire il problema, non a sostituire il soccorso.'),
            self::entry('visibile-a-tutti', 110, 'La segnalazione è visibile a tutti?', 'Sì. L’elenco pubblico mostra le segnalazioni e il loro stato, così chi abita vicino vede se il problema è già stato preso in carico. Nel testo e nelle foto non inserire dati di altre persone.'),
            self::entry('non-trovo-la-risposta', 120, 'Non trovo la risposta. Cosa faccio?', 'Se il tema è un disservizio in città, invia una segnalazione. Se non riesci ad accedere o a seguire una pratica, entra nell’area personale oppure usa «Traccia» con il codice.'),
        ];
    }

    /**
     * @return list<FaqEntry>
     */
    private static function english(): array
    {
        return [
            self::entry('come-invio-una-segnalazione', 10, 'How do I send a report?', 'Open “Report”, describe what is wrong, choose a category (roads, lighting, waste, greenery) and mark the place on the map. A photo of the exact spot helps the office understand it without an extra inspection.'),
            self::entry('devo-registrarmi', 20, 'Do I need an account to report?', 'An account is required to receive updates on the case: name, email, password and consent to the privacy notice. Without an account you can still read the public list of reports.'),
            self::entry('nome-visibile', 30, 'Does my name appear on the report?', 'The office sees the account that sent the case. The public list shows the problem, the place and the status. Your name is not published.'),
            self::entry('allego-una-foto', 40, 'How do I attach a photo?', 'The form has an image field. A daytime photo with a recognisable reference (house number, stop, junction) is worth more than a long description. Do not include plates, faces or other people’s documents.'),
            self::entry('indico-il-luogo', 50, 'How do I point to the place?', 'Search for the address or move the point on the map. If it is a junction, a park or a pavement, add the side of the street or a nearby reference in the text.'),
            self::entry('seguo-lo-stato', 60, 'How do I follow the status?', 'Use the code you receive with “Track”, even without signing in. The personal area lists every case on your account. Status moves from received, to in progress, to closed.'),
            self::entry('tempi-di-risposta', 70, 'How long for a first reply?', 'It depends on the office and the urgency. A hole in the carriageway and a full bin do not have the same priority. The case stays readable, and a note is added when there is an outcome.'),
            self::entry('chi-legge', 80, 'Who reads the report?', 'The office responsible for the category: roads, lighting, waste or greenery. FixCity does not replace the office. It keeps the thread between your request and the people who must act.'),
            self::entry('aggiorno-una-segnalazione', 90, 'Can I update a report I already sent?', 'Do not open a second one on the same spot. If you have a new photo, a time or a more precise address, add it to the open case so it stays in the same file.'),
            self::entry('pericolo-immediato', 100, 'How do I report an immediate danger?', 'For an ongoing risk — a gas leak, a tree on the carriageway, a person in danger — call the emergency numbers. A report on the site is for follow-up, not a substitute for rescue.'),
            self::entry('visibile-a-tutti', 110, 'Is the report visible to everyone?', 'Yes. The public list shows reports and their status, so neighbours can see whether the problem is already being handled. Do not put other people’s data in the text or the photos.'),
            self::entry('non-trovo-la-risposta', 120, 'I cannot find the answer. What should I do?', 'If it is a problem in the city, send a report. If you cannot sign in or follow a case, open the personal area or use “Track” with the code.'),
        ];
    }

    /**
     * @return list<FaqEntry>
     */
    private static function german(): array
    {
        return [
            self::entry('come-invio-una-segnalazione', 10, 'Wie sende ich eine Meldung?', 'Öffne „Melden“, beschreibe den Mangel, wähle die Kategorie (Straße, Beleuchtung, Abfall, Grün) und setze den Ort auf der Karte. Ein Foto der genauen Stelle erspart dem Amt einen zusätzlichen Augenschein.'),
            self::entry('devo-registrarmi', 20, 'Brauche ich ein Konto, um zu melden?', 'Für die Updates zum Vorgang brauchst du ein Konto: Name, E-Mail, Passwort und die Zustimmung zur Datenschutzerklärung. Ohne Konto kannst du die öffentliche Liste der Meldungen lesen.'),
            self::entry('nome-visibile', 30, 'Erscheint mein Name bei der Meldung?', 'Das Amt sieht das Konto, mit dem du den Vorgang gesendet hast. In der öffentlichen Liste stehen Problem, Ort und Status. Dein Name wird nicht veröffentlicht.'),
            self::entry('allego-una-foto', 40, 'Wie füge ich ein Foto hinzu?', 'Im Formular gibt es ein Bildfeld. Ein Foto bei Tageslicht mit einem erkennbaren Bezug (Hausnummer, Haltestelle, Kreuzung) ist mehr wert als ein langer Text. Keine Kennzeichen, Gesichter oder fremde Dokumente.'),
            self::entry('indico-il-luogo', 50, 'Wie gebe ich den Ort an?', 'Suche die Adresse oder verschiebe den Punkt auf der Karte. Bei einer Kreuzung, einem Park oder einem Gehweg ergänze im Text die Straßenseite oder einen nahen Bezug.'),
            self::entry('seguo-lo-stato', 60, 'Wie verfolge ich den Status?', 'Mit dem Code, den du erhältst, nutzt du „Verfolgen“, auch ohne Anmeldung. Im persönlichen Bereich siehst du alle Vorgänge deines Kontos. Der Status geht von eingegangen über in Bearbeitung zu abgeschlossen.'),
            self::entry('tempi-di-risposta', 70, 'Wie lange dauert eine erste Antwort?', 'Das hängt vom Amt und von der Dringlichkeit ab. Ein Loch in der Fahrbahn und ein voller Papierkorb haben nicht dieselbe Priorität. Der Vorgang bleibt einsehbar, und bei einem Ergebnis steht eine Notiz beim Status.'),
            self::entry('chi-legge', 80, 'Wer liest die Meldung?', 'Das Amt, das für die Kategorie zuständig ist: Straßen, Beleuchtung, Abfall oder Grün. FixCity ersetzt das Amt nicht. Es hält den Faden zwischen deiner Anfrage und denen, die handeln müssen.'),
            self::entry('aggiorno-una-segnalazione', 90, 'Kann ich eine gesendete Meldung ergänzen?', 'Eröffne keine zweite am selben Punkt. Ein neues Foto, eine Uhrzeit oder eine genauere Adresse gehört in den offenen Vorgang, damit alles in derselben Akte bleibt.'),
            self::entry('pericolo-immediato', 100, 'Wie melde ich eine unmittelbare Gefahr?', 'Bei einem laufenden Risiko — Gasaustritt, Baum auf der Fahrbahn, Person in Gefahr — ruf die Notrufnummern an. Die Meldung auf der Seite dient der weiteren Bearbeitung, nicht dem Einsatz.'),
            self::entry('visibile-a-tutti', 110, 'Ist die Meldung für alle sichtbar?', 'Ja. Die öffentliche Liste zeigt Meldungen und Status, damit Nachbarn sehen, ob das Problem schon bearbeitet wird. Keine Daten anderer Personen in Text oder Fotos.'),
            self::entry('non-trovo-la-risposta', 120, 'Ich finde die Antwort nicht. Was tun?', 'Geht es um einen Mangel in der Stadt, sende eine Meldung. Wenn du dich nicht anmelden oder einen Vorgang nicht verfolgen kannst, öffne den persönlichen Bereich oder nutze „Verfolgen“ mit dem Code.'),
        ];
    }

    /**
     * @return list<FaqEntry>
     */
    private static function spanish(): array
    {
        return [
            self::entry('come-invio-una-segnalazione', 10, '¿Cómo envío una incidencia?', 'Abre «Señalar», describe lo que no funciona, elige la categoría (calzada, alumbrado, residuos, zonas verdes) y marca el lugar en el mapa. Una foto del punto exacto ayuda a la oficina sin una inspección de más.'),
            self::entry('devo-registrarmi', 20, '¿Necesito una cuenta para señalar?', 'Para recibir las actualizaciones del expediente hace falta una cuenta: nombre, correo, contraseña y el consentimiento a la información de privacidad. Sin cuenta puedes consultar la lista pública de incidencias.'),
            self::entry('nome-visibile', 30, '¿Aparece mi nombre en la incidencia?', 'La oficina ve la cuenta con la que enviaste el expediente. En la lista pública quedan el problema, el lugar y el estado. Tu nombre no se publica.'),
            self::entry('allego-una-foto', 40, '¿Cómo adjunto una foto?', 'El formulario tiene un campo de imágenes. Una foto de día, con una referencia reconocible (número, parada, cruce), vale más que un texto largo. No encuadres matrículas, caras ni documentos de otras personas.'),
            self::entry('indico-il-luogo', 50, '¿Cómo indico el lugar?', 'Busca la dirección o mueve el punto en el mapa. Si es un cruce, un parque o una acera, añade en el texto el lado de la calle o una referencia cercana.'),
            self::entry('seguo-lo-stato', 60, '¿Cómo sigo el estado?', 'Con el código que recibes usas «Seguir», también sin entrar. En el área personal ves todos los expedientes de tu cuenta. El estado pasa de recibida a en curso y luego a cerrada.'),
            self::entry('tempi-di-risposta', 70, '¿Cuánto tarda una primera respuesta?', 'Depende de la oficina y de la urgencia. Un bache en la calzada y una papelera llena no tienen la misma prioridad. El expediente sigue consultable y, cuando hay un resultado, aparece una nota en el estado.'),
            self::entry('chi-legge', 80, '¿Quién lee la incidencia?', 'La oficina competente según la categoría: calzada, alumbrado, residuos o zonas verdes. FixCity no sustituye a la oficina: mantiene el hilo entre tu petición y quien debe actuar.'),
            self::entry('aggiorno-una-segnalazione', 90, '¿Puedo actualizar una incidencia ya enviada?', 'No abras otra sobre el mismo punto. Si tienes una foto nueva, un horario o una dirección más precisa, añádelo al expediente abierto para que quede en el mismo expediente.'),
            self::entry('pericolo-immediato', 100, '¿Cómo señalo un peligro inmediato?', 'Ante un riesgo en curso — fuga de gas, árbol en la calzada, persona en peligro — llama a los números de emergencia. La incidencia en el sitio sirve para el seguimiento, no sustituye al socorro.'),
            self::entry('visibile-a-tutti', 110, '¿La incidencia es visible para todos?', 'Sí. La lista pública muestra las incidencias y su estado, para que quien vive cerca vea si el problema ya está en curso. No pongas datos de otras personas en el texto ni en las fotos.'),
            self::entry('non-trovo-la-risposta', 120, 'No encuentro la respuesta. ¿Qué hago?', 'Si se trata de un desperfecto en la ciudad, envía una incidencia. Si no puedes entrar o seguir un expediente, abre el área personal o usa «Seguir» con el código.'),
        ];
    }

    /**
     * @return array{slug: non-empty-string, sort_order: int, question: non-empty-string, answer: non-empty-string}
     */
    private static function entry(string $slug, int $sortOrder, string $question, string $answer): array
    {
        if ($slug === '' || $question === '' || $answer === '') {
            throw new \InvalidArgumentException('FAQ entries require non-empty text.');
        }

        return [
            'slug' => $slug,
            'sort_order' => $sortOrder,
            'question' => $question,
            'answer' => $answer,
        ];
    }
}
