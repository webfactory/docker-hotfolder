#!/bin/sh -e

[ -z "$UPLOAD_URL" ] && echo "Die Umgebungsvariable UPLOAD_URL muss gesetzt werden" && exit 1

sh -c 'while true; do sleep $PURGE_INTERVAL; bin/console hotfolder:purge "$ARCHIVE" ; done' &

inotifywait --quiet --monitor --event create,modify --recursive $HOTFOLDER | while read file; do
    bin/console hotfolder:upload --form-field-name=$FORM_FIELD_NAME $UPLOAD_URL $HOTFOLDER "$PATTERN" $ARCHIVE
done
