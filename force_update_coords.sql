-- Force update with exact names or broad matches for better results
USE urgences_antsiranana;

-- CHRR Tanambao (Hopitale BE)
UPDATE service SET latitude = -12.28541210, longitude = 49.29215430 WHERE libelle LIKE '%Hopitale BE%';

-- Hôpital Militaire (Homi)
UPDATE service SET latitude = -12.27114250, longitude = 49.29132100 WHERE libelle LIKE '%Homi%';

-- Commissariat / Police
UPDATE service SET latitude = -12.27312000, longitude = 49.29184000 WHERE id_type = 3;

-- Sapeurs-Pompiers
UPDATE service SET latitude = -12.27785000, longitude = 49.28990000 WHERE id_type = 2;

-- Gendarmerie
UPDATE service SET latitude = -12.28115000, longitude = 49.28912000 WHERE libelle LIKE '%Gendarmerie%';
