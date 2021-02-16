SELECT familyName
FROM Laureate L LEFT OUTER JOIN Wins W ON L.id=W.lid
GROUP BY familyName
HAVING COUNT(familyName) >= 5;