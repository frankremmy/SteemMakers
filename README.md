#### SteemMakers
SteemMakers is a community which focuses on uniting, helping and rewarding makers and DIYers on the Steem blockchain. You can find more info about us and our motivation on the [website's about page](https://steemmakers.com/about.php).

#### Contributing
This project is in it's infancy. The repository contains the following components:

- Database functionality
- Voting and commenting bot
- Website

If you want to contribute it is advised to first contact the team on our [Discord](https://discord.gg/EFGbRuW).


#### Compile Vue.js with Docker

We are trying to make it as easy as possible to contribute to the development of steemmakers.
The webpage does run with Vue.js and this requires a lot of other JS libs (e.g. Node.js) to be installed.
To make the developer life easyer you can use the docker batch files to compile and test your code.
You only need to install docker (https://www.docker.com/) and everything else is automaticaly downloaded 
when you compile with the batch files.

Usage on Windows:

Run docker-npm-build.bat in your SteemMakers folder when you just change some vue.js code.

Run docker-npm-build-install.bat when you changed the package.json or downloaded a new version of it.

Run docker-npm-build-install.bat the first time when you downloaded the repository.


