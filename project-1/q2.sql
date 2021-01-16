SELECT first, last
FROM Actor, MovieActor, Movie
WHERE Actor.id=aid 
AND MovieActor.mid=Movie.id 
AND title="Die Another Day";
