select 
    Cha_ID, Cha_Name,
    345*Challenges.Cha_ID as 'Mumero de proyectos',
    34+Cha_ID as 'numero de equipos',
    11+Challenges.Cha_ID as 'numero de mentores'
from Challenges
where Cha_Organizer=1;