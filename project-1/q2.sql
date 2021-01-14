-- SELECT first, last
-- FROM Actor
-- INNER JOIN MovieActor ON Actor.id=aid
-- INNER JOIN Movie ON mid=Movie.id 
-- WHERE title="Die Another Day";

SELECT first, last
FROM Actor, MovieActor, Movie
WHERE Actor.id=aid 
AND MovieActor.mid=Movie.id 
AND title="Die Another Day";
