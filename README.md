# Docker image to watch a folder and HTTP POST uploaded files

This repository contains a very simple Symfony application to HTTP POST all files from
a given directory that have been modified at least 30 seconds ago to a given URL, and to 
move them to an `archive` folder afterwards. And a second command to prune older files
from that archive directory.

Started from a shell script in combination with `inotify`, this can be used to do
monitor a directory exported over NFS, SMB or FTP and forward new files to some API endpoint.

## Docker Build Process

Since Docker changed their terms of service, we currently do not build images automatically.

## Credits, Copyright and License

This action was written by webfactory GmbH, Bonn, Germany. We're a software development
agency with a focus on PHP (mostly [Symfony](http://github.com/symfony/symfony)). If you're a
developer looking for new challenges, we'd like to hear from you!

- <https://www.webfactory.de>
- <https://twitter.com/webfactory>

Copyright 2019 – 2022 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
