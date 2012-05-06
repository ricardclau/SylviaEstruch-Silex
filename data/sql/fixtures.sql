SET NAMES 'utf8';
TRUNCATE TABLE categorias_pintura;
INSERT INTO categorias_pintura (title_ca, title_es, title_en) VALUES
('Realista', 'Realista', 'Realistic'),
('Transició', 'Transición', 'Transition'),
('Impressionista', 'Impresionista', 'Impressionist'),
('Post-impressionista', 'Post-impresionista', 'Post-impressionist'),
('Creació', 'Creación', 'Creation'),
('Últimes creacions', 'Últimas creaciones', 'Latest creations');
TRUNCATE TABLE categorias_teatro;
INSERT INTO categorias_teatro (title_ca, title_es, title_en) VALUES
('Obres de teatre', 'Obras de teatro', 'Theater plays'),
('Performances', 'Performances', 'Performances');
