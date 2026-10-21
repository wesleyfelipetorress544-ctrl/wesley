<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['Dom Casmurro', 'Machado de Assis', 'Bento Santiago relembra a juventude e seu relacionamento com Capitu, em uma narrativa sobre memória, ciúme e confiança.'],
            ['Memórias Póstumas de Brás Cubas', 'Machado de Assis', 'Um narrador já morto conta sua vida com ironia e questiona os valores da sociedade brasileira do século XIX.'],
            ['O Alienista', 'Machado de Assis', 'O médico Simão Bacamarte funda um hospício em Itaguaí e põe em dúvida os limites entre razão e loucura.'],
            ['Quincas Borba', 'Machado de Assis', 'Rubião herda uma fortuna e uma filosofia peculiar, mas encontra ambição e interesses ao seu redor.'],
            ['Iracema', 'José de Alencar', 'O encontro entre Iracema e Martim dá origem a uma narrativa romântica ligada à formação do Ceará.'],
            ['O Guarani', 'José de Alencar', 'Peri protege a família de Dom Antônio de Mariz em uma aventura ambientada no Brasil colonial.'],
            ['Senhora', 'José de Alencar', 'Aurélia Camargo enfrenta as convenções sociais e os interesses por trás do casamento no Rio de Janeiro imperial.'],
            ['O Cortiço', 'Aluísio Azevedo', 'A vida de moradores de um cortiço carioca revela conflitos sociais e transformações urbanas no século XIX.'],
            ['Triste Fim de Policarpo Quaresma', 'Lima Barreto', 'O patriota Policarpo Quaresma tenta transformar o país e confronta a burocracia e as desigualdades de seu tempo.'],
            ['Clara dos Anjos', 'Lima Barreto', 'Clara, uma jovem negra dos subúrbios do Rio, enfrenta preconceito e falsas promessas.'],
            ['Vidas Secas', 'Graciliano Ramos', 'Fabiano, Sinhá Vitória e seus filhos atravessam a seca e a pobreza no sertão nordestino.'],
            ['Capitães da Areia', 'Jorge Amado', 'Um grupo de meninos enfrenta a pobreza e constrói laços de amizade nas ruas de Salvador.'],
            ['A Hora da Estrela', 'Clarice Lispector', 'Macabéa, uma jovem nordestina no Rio de Janeiro, busca espaço e reconhecimento em uma vida difícil.'],
            ['Grande Sertão: Veredas', 'João Guimarães Rosa', 'Riobaldo narra suas travessias pelo sertão, entre guerras, escolhas e dúvidas sobre o bem e o mal.'],
            ['Morte e Vida Severina', 'João Cabral de Melo Neto', 'Severino percorre o caminho do sertão ao Recife em um auto sobre migração e esperança.'],
            ['Memórias de um Sargento de Milícias', 'Manuel Antônio de Almeida', 'As aventuras de Leonardo retratam com humor os costumes do Rio de Janeiro no início do século XIX.'],
            ['Macunaíma', 'Mário de Andrade', 'O herói sem nenhum caráter atravessa o Brasil em uma narrativa modernista e fantástica.'],
            ['O Meu Pé de Laranja Lima', 'José Mauro de Vasconcelos', 'Zezé encontra imaginação e afeto enquanto enfrenta as dificuldades de sua infância.'],
            ['Turma da Mônica: Laços', 'Vitor Cafaggi e Lu Cafaggi', 'Mônica, Cebolinha, Magali e Cascão se unem em uma aventura em quadrinhos para encontrar Floquinho.'],
            ['Turma da Mônica: Lições', 'Vitor Cafaggi e Lu Cafaggi', 'Os amigos aprendem sobre responsabilidade e convivência em uma aventura brasileira em quadrinhos.'],
            ['One Piece, Vol. 1', 'Eiichiro Oda', 'Monkey D. Luffy parte em busca do tesouro One Piece e reúne companheiros para sua tripulação.'],
            ['Naruto, Vol. 1', 'Masashi Kishimoto', 'Naruto Uzumaki sonha em se tornar Hokage enquanto começa seu treinamento ninja.'],
            ['Dragon Ball, Vol. 1', 'Akira Toriyama', 'Goku e Bulma iniciam uma jornada para encontrar as Esferas do Dragão.'],
            ['Demon Slayer: Kimetsu no Yaiba, Vol. 1', 'Koyoharu Gotouge', 'Tanjiro parte em uma jornada para proteger sua irmã e enfrentar demônios.'],
            ['My Hero Academia, Vol. 1', 'Kohei Horikoshi', 'Izuku Midoriya busca se tornar um herói em um mundo onde quase todos têm poderes.'],
            ['Death Note, Vol. 1', 'Tsugumi Ohba e Takeshi Obata', 'Light Yagami encontra um caderno sobrenatural capaz de matar quem tiver o nome escrito nele.'],
            ['Fullmetal Alchemist, Vol. 1', 'Hiromu Arakawa', 'Os irmãos Edward e Alphonse Elric procuram uma forma de recuperar o que perderam ao usar alquimia.'],
            ['Sailor Moon, Vol. 1', 'Naoko Takeuchi', 'Usagi Tsukino descobre seu destino como Sailor Moon e começa a proteger a Terra.'],
        ];

        foreach ($books as [$title, $author, $description]) {
            Book::updateOrCreate(
                ['title' => $title],
                ['author' => $author, 'description' => $description],
            );
        }
    }
}
