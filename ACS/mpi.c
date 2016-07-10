#include <stdio.h>
#include <stdlib.h>
#include <mpi.h>
#include <pthread.h>

#define DEBUG 1

int treads;
int  nextProc, prevProc;
char name[32];
int kProd;
int hA, wA, hB, wB;
int sizeA, sizeB, partA, partB;
int currentRank, clusterSize, virtualClusterSize, realPartA;
int * clusterPerformance;
MPI_Status Status;

int  * A, * B, * C, * A_network, * tmpA;
long data[1], CRC;
int ids[10] = {0,1,2,3};

pthread_t thrs[4];
pthread_attr_t attrs;

void setA(int step)
{
    int j;
    int count = step * wA * partA + 1;
    #if DEBUG > 1
        printf("send A to %i - ", step);
    #endif
    for(j = 0; j < wA * partA; j++){
        if (count <= hA * wA) {
            A[j] = count;
            count ++;
        } else {
            A[j] = 0;
        }
        #if DEBUG > 1
            printf("%i ", A[j]);
        #endif
    }
    #if DEBUG > 1
        printf("\n");
    #endif
}

void setB(int step, int prod, int toRank)
{
    int i,j,k;
    int count = wA*hA + step*partB + 1, tmp1, tmp2, tmp3;
    #if DEBUG > 1
        printf("Send B to %i - ", toRank);
    #endif
    for (k = 0; k < prod; k++) {
        for (i = 0; i < partB; i++) {
            for (j = 0; j < hB; j++) {
                if ((step*partB+i + k*partB) < wB) {
                    B[k*partB*hB+j+i*hB] = count + j*wB + i + k*partB;
                } else {
                    B[k*partB*hB+j+i*hB] = 0;
                }
                #if DEBUG > 1
                    printf("%i (%i) ", B[k*partB*hB+j+i*hB], k*partB*hB+j+i*hB);
                #endif
            }
        }
    }
    #if DEBUG > 1
        printf("\n");
    #endif
}

void* calculationOfTheMatrixThread(void* me)
{
    int i,j,l, tmp;
    int offset = *((int*)me);
    for (l = offset; l < partA; l+=treads) {
        for (j = 0; j < partB*kProd; j++) {
            tmp = 0;
            for (i = 0; i < wA; i++) {
                tmp += A[i+l*wA] * B[i+j*hB];
                #if DEBUG > 1
                    printf("[%i][%i]Set [%ix%i] [%i x %i] + %i\n", currentRank, offset, realPartA*partA + l + j/kProd, currentRank*partB + j%kProd, A[i+l*wA], B[i+j*hB], A[i+l*wA] * B[i+j*hB]);
                #endif
            }
            #if DEBUG > 1
                printf("[%i][%i]Set [%ix%i][%i] = %i\n", currentRank, offset, (realPartA*partA+j*sizeA+l)/partB, currentRank*partB + j%kProd, realPartA*partA+j*sizeA+l,  tmp);
            #endif
            C[realPartA*partA+j*sizeA+l] = tmp;
        }
    }
}
void calculationOfTheMatrix()
{
    int i;
    for(i = 0; i<treads; i++) {
        if (0!=pthread_create(&thrs[i], &attrs, calculationOfTheMatrixThread, &ids[i])) {
            perror("Cannot create a thread");
            abort();
        }
    }
    MPI_Sendrecv(A, wA*partA, MPI_INT, nextProc, 0, A_network, wA*partA, MPI_INT, prevProc, 0, MPI_COMM_WORLD, &Status);
    for(i = 0; i<treads; i++) {
        if (0!=pthread_join(thrs[i], NULL)) {
            perror("Cannot join a thread");
            abort();
        }
    }
    tmpA = A;
    A = A_network;
    A_network = tmpA;
}

int up(float v)
{
    int i = v;
    if (v - i > 0.000001) {
        i++;
    }
    return i;
}

void init()
{
    int tmp[1],S,i;
    gethostname(name, 32);
    if (name[2] == '9') {
        kProd = 6;
    } else if((name[3] == '8')  || (name[2] == '7' && name[3] == '0')) {
        kProd = 2;
    } else {
        kProd = 3;
    }
    tmp[0] = kProd;
    if (currentRank == 0) {
        clusterPerformance = (int*) malloc (clusterSize * sizeof(int));
        S = tmp[0];
        clusterPerformance[0] = tmp[0];
        for(i = 1; i < clusterSize; i++){
            MPI_Recv(tmp, 1, MPI_INT, i, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
            clusterPerformance[i] = tmp[0];
            S += tmp[0];
        }
        tmp[0] = S;
        for(i = 1; i < clusterSize; i++){
            MPI_Send(tmp, 1, MPI_INT, i, 0, MPI_COMM_WORLD);
        }
        virtualClusterSize = tmp[0];
    } else {
        MPI_Send(tmp, 1, MPI_INT, 0, 0, MPI_COMM_WORLD);
        MPI_Recv(tmp, 1, MPI_INT, 0, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
        virtualClusterSize = tmp[0];
    }
    partA = up((float) hA / (float) clusterSize);
    partB = up((float) wB / (float) virtualClusterSize);
    sizeA = partA * clusterSize;
    sizeB = partB * virtualClusterSize;
}
 
int main (int argc, char* argv[])
{
    double t1, t2, startCalc, endCalc, startProg, endProg;
    float timeSum, timeBuff[1];
    if (argc != 5) {
        printf("Set input.\n");
        return 0;
    }
    int provided;
    MPI_Init_thread(&argc, &argv, MPI_THREAD_FUNNELED, &provided);
    if (0!=pthread_attr_init(&attrs)) {
        perror("Cannot initialize attributes");
        abort();
    };
    if (0!=pthread_attr_setdetachstate(&attrs, PTHREAD_CREATE_JOINABLE)) {
        perror("Error in setting attributes");
        abort();
    }
    startProg = MPI_Wtime();
    hA = atoi(argv[1]);
    wA = atoi(argv[2]);
    hB = atoi(argv[2]);
    wB = atoi(argv[3]);
    treads= atoi(argv[4]);
    int  i, j, realPrevA;
    long data[1], CRC;

    MPI_Comm_rank (MPI_COMM_WORLD, &currentRank);
    MPI_Comm_size (MPI_COMM_WORLD, &clusterSize);

    init();
    A = (int*) malloc (wA * partA * sizeof(int));
    A_network = (int*) malloc (wA * partA * sizeof(int));
    B = (int*) malloc (kProd * hB * partB * sizeof(int));
    C = (int*) malloc (kProd * partA * sizeB * sizeof(int));
    #if DEBUG > 0
        printf( "Hello from process %d of %d [%s]\n", currentRank, clusterSize, name);
    #endif
    if (currentRank == 0) {
        printf("Start task %ix%i - %ix%i[clusterSize:%i partA:%i partB:%i virtualClusterSize:%i]\n", hA, wA, hB, wB, clusterSize, partA, partB, virtualClusterSize);
        int summSteps = kProd ;
        for(i = 1; i < clusterSize; i++){
            setA(i);
            MPI_Send(A, wA*partA, MPI_INT, i, 0, MPI_COMM_WORLD);
            for (j = 0; j < clusterPerformance[i]; j++) {
                setB(summSteps, 1, i);
                MPI_Send(B, hB*partB, MPI_INT, i, 0, MPI_COMM_WORLD);
                summSteps++;
            }
        }
        setA(0);
        setB(0, kProd, 0);
    } else {
        MPI_Recv(A, wA*partA, MPI_INT, 0, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
        if (kProd == 1) {
            MPI_Recv(B, hB*partB, MPI_INT, 0, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
        } else {
            int * tBuf = (int*) malloc (hB * partB * sizeof(int));
            for (i = 0; i < kProd; i++) {
                MPI_Recv(tBuf, hB*partB, MPI_INT, 0, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
                for (j = 0; j < hB*partB; j++) {
                    B[i*hB*partB + j] = tBuf[j];
                }
            }
        }
    }
    for (i = 0; i < partB * sizeA; i++) {
        C[i] = 0;
    }
    startCalc = MPI_Wtime();
    // Init calculat
    if ((currentRank + 1) == clusterSize) {
        nextProc = 0;
    } else {
        nextProc = currentRank + 1;
    }
    if (currentRank == 0) {
        prevProc = clusterSize - 1;
    } else {
        prevProc = currentRank - 1;
    }
    realPartA = currentRank;
    calculationOfTheMatrix();
    realPrevA = prevProc;
    // Start main work
    for (j = 1; j < clusterSize; j++) {
        realPartA = realPrevA;
        calculationOfTheMatrix();
        realPrevA --;
        if (realPrevA == -1) realPrevA += clusterSize;
    }
    endCalc = MPI_Wtime();
    // Get result
    data[0] = 0;
    for (j = 0; j < partB * sizeA * kProd; j++) {
        data[0] += C[j];
    }
    timeBuff[0] = timeSum;
    if (currentRank == 0) {
        CRC = data[0];
        for(i = 1; i < clusterSize; i++){
            MPI_Recv(data, 1, MPI_LONG, i, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
            MPI_Recv(timeBuff, 1, MPI_FLOAT, i, 0, MPI_COMM_WORLD, MPI_STATUS_IGNORE);
            timeSum += timeBuff[0];
            CRC += data[0];
        }
        endProg = MPI_Wtime();
        printf(
            "Result: %ld, elapsed time: %.4f, all time: %.4f\n",
            CRC,
            endCalc - startCalc,
            endProg - startProg
        );
    }else{
        MPI_Send(data, 1, MPI_LONG, 0, 0, MPI_COMM_WORLD);
        MPI_Send(timeBuff, 1, MPI_FLOAT, 0, 0, MPI_COMM_WORLD);
    }
    MPI_Finalize();
    return 0;
}