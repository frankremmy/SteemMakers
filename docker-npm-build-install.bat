@ECHO OFF
SET steemmakerpath=%~dp0
SET steemmakerpath=%steemmakerpath%website\
ECHO %steemmakerpath%
@ECHO OFF
docker run --rm -v %steemmakerpath%:/src -w /src -p 8080:8080 node:8.9.0 bash -c "npm install && npm run dockerbuild"
