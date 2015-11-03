:-dynamic trans/3.
show_menu:-consult('bd.pl'),
 repeat,
 write('1) Show list'),nl,
 write('2) Add'),nl,
 write('3) Search'),nl,
 write('4) Remove'),nl,
 write('5) Exit'),nl,nl,
 write('What do you want?: (1-5) '),
 read(X),
 X<6,
 process(X),
 X=5,!.
process(5).
process(4):- write('What num do you want remove?'),
        read(Num),nl,
        retract(trans(_,Num,_)),
        tell('bd.pl'),
        listing(trans/3),
        told.
process(1):-listing(trans).
process(2):-write('Name: '),
       read(Type),nl,
       write('Num: '),
       read(Num), nl,
       write('Path: '),
       read(Stop),
       assertz(trans(Type,Num,Stop)),
       tell('bd.pl'),
       listing(trans/3),
       told.
process(3):-write('First bus stop: '),
       read(D1),
       write('Second bus stop: '),
       read(D2),
       trans(X,M,Y),
       member(D1,Y),
       member(D2,Y),
       write(trans(X,M,Y)), nl.
